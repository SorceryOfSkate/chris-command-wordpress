[CmdletBinding()]
param(
    [string]$Version = ''
)

$ErrorActionPreference = 'Stop'
$repositoryRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path
$sourceHeader = Get-Content -Raw -LiteralPath (Join-Path $repositoryRoot 'chris-command.php')
if ($sourceHeader -notmatch '(?m)^ \* Version:\s+([0-9]+\.[0-9]+\.[0-9]+)\s*$') {
    throw 'Unable to read the source plugin version header.'
}
if (-not $Version) {
    $Version = $Matches[1]
} elseif ($Matches[1] -ne $Version) {
    throw "Requested version $Version does not match source plugin header $($Matches[1])."
}

$archivePath = Join-Path $repositoryRoot "dist/chris-command-$Version.zip"
$checksumPath = "$archivePath.sha256"

if (-not (Test-Path -LiteralPath $archivePath)) {
    throw "Release archive not found: $archivePath"
}
if (-not (Test-Path -LiteralPath $checksumPath)) {
    throw "Checksum not found: $checksumPath"
}

$temporaryRoot = [IO.Path]::GetFullPath([IO.Path]::Combine([IO.Path]::GetTempPath(), "chris-command-verify-$([Guid]::NewGuid())"))
$systemTemp = [IO.Path]::GetFullPath([IO.Path]::GetTempPath())
if (-not $temporaryRoot.StartsWith($systemTemp, [StringComparison]::OrdinalIgnoreCase)) {
    throw 'Refusing to use an unexpected temporary path.'
}

try {
    New-Item -ItemType Directory -Path $temporaryRoot | Out-Null
    Expand-Archive -LiteralPath $archivePath -DestinationPath $temporaryRoot

    $topLevel = @(Get-ChildItem -LiteralPath $temporaryRoot -Force)
    if (1 -ne $topLevel.Count -or -not $topLevel[0].PSIsContainer -or 'chris-command' -ne $topLevel[0].Name) {
        throw 'Release ZIP must contain exactly one top-level chris-command directory.'
    }

    $pluginRoot = $topLevel[0].FullName
    $required = @(
        'chris-command.php',
        'includes/contracts/interface-module.php',
        'includes/core/class-module-registry.php',
        'includes/core/class-plugin.php',
        'includes/modules/news/class-news-module.php',
        'includes/modules/news/class-news-service.php',
        'includes/modules/news/class-news-renderer.php',
        'includes/modules/news/class-news-rest-controller.php',
        'includes/modules/dashboard/class-dashboard-module.php',
        'includes/modules/dashboard/class-dashboard-renderer.php',
        'blocks/news/block.json',
        'blocks/news/index.js',
        'blocks/news/style.css',
        'blocks/dashboard/block.json',
        'blocks/dashboard/index.js',
        'assets/css/dashboard.css',
        'assets/js/dashboard.js',
        'templates/chris-command-dashboard.php',
        'LICENSE',
        'readme.txt',
        'uninstall.php'
    )
    foreach ($relative in $required) {
        if (-not (Test-Path -LiteralPath (Join-Path $pluginRoot $relative))) {
            throw "Required release file is missing: $relative"
        }
    }

    $forbidden = @('.git', '.github', 'docs', 'tests', 'tools', 'node_modules', 'vendor', 'composer.json', 'package.json', 'phpcs.xml.dist')
    foreach ($relative in $forbidden) {
        if (Test-Path -LiteralPath (Join-Path $pluginRoot $relative)) {
            throw "Development-only release path is present: $relative"
        }
    }

    $pluginHeader = Get-Content -Raw -LiteralPath (Join-Path $pluginRoot 'chris-command.php')
    $readme = Get-Content -Raw -LiteralPath (Join-Path $pluginRoot 'readme.txt')
    $blockMetadata = Get-Content -Raw -LiteralPath (Join-Path $pluginRoot 'blocks/news/block.json') | ConvertFrom-Json
    $dashboardMetadata = Get-Content -Raw -LiteralPath (Join-Path $pluginRoot 'blocks/dashboard/block.json') | ConvertFrom-Json
    $packageMetadata = Get-Content -Raw -LiteralPath (Join-Path $repositoryRoot 'package.json') | ConvertFrom-Json
    $changelog = Get-Content -Raw -LiteralPath (Join-Path $repositoryRoot 'CHANGELOG.md')
    if ($pluginHeader -notmatch "(?m)^ \* Version:\s+$([regex]::Escape($Version))\s*$") {
        throw 'Plugin header version does not match the release version.'
    }
    if ($readme -notmatch "(?m)^Stable tag:\s+$([regex]::Escape($Version))\s*$") {
        throw 'readme.txt stable tag does not match the release version.'
    }
    if ($blockMetadata.version -ne $Version) {
        throw 'News block metadata version does not match the release version.'
    }
    if ($dashboardMetadata.version -ne $Version) {
        throw 'Dashboard block metadata version does not match the release version.'
    }
    if ($packageMetadata.version -ne $Version) {
        throw 'package.json version does not match the release version.'
    }
    if ($changelog -notmatch "(?m)^## $([regex]::Escape($Version))(?:\s|$)") {
        throw 'CHANGELOG.md does not contain the release version.'
    }

    $expectedHash = ((Get-Content -Raw -LiteralPath $checksumPath).Trim() -split '\s+')[0]
    $actualHash = (Get-FileHash -Algorithm SHA256 -LiteralPath $archivePath).Hash.ToLowerInvariant()
    if ($expectedHash -ne $actualHash) {
        throw 'Release checksum does not match the ZIP.'
    }

    Write-Output "Release verification passed: chris-command-$Version.zip ($actualHash)"
}
finally {
    if (Test-Path -LiteralPath $temporaryRoot) {
        $resolvedTemporaryRoot = (Resolve-Path -LiteralPath $temporaryRoot).Path
        if ($resolvedTemporaryRoot.StartsWith($systemTemp, [StringComparison]::OrdinalIgnoreCase)) {
            Remove-Item -LiteralPath $resolvedTemporaryRoot -Recurse -Force
        }
    }
}
