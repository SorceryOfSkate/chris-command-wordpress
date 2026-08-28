[CmdletBinding()]
param(
    [string]$Version = '0.1.0'
)

$ErrorActionPreference = 'Stop'
$repositoryRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path
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
    if ($pluginHeader -notmatch "(?m)^ \* Version:\s+$([regex]::Escape($Version))\s*$") {
        throw 'Plugin header version does not match the release version.'
    }
    if ($readme -notmatch "(?m)^Stable tag:\s+$([regex]::Escape($Version))\s*$") {
        throw 'readme.txt stable tag does not match the release version.'
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
