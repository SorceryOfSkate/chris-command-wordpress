[CmdletBinding()]
param(
    [string]$Version = '0.1.0'
)

$ErrorActionPreference = 'Stop'
$repositoryRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path
$distPath = Join-Path $repositoryRoot 'dist'
$archivePath = Join-Path $distPath "chris-command-$Version.zip"
$checksumPath = "$archivePath.sha256"

if (-not (Test-Path -LiteralPath (Join-Path $repositoryRoot '.git'))) {
    throw 'Release builds require an initialized Git repository.'
}

$status = git -C $repositoryRoot status --porcelain
if ($LASTEXITCODE -ne 0) {
    throw 'Unable to inspect Git status.'
}
if ($status) {
    throw 'Release builds require a clean working tree.'
}

$header = Get-Content -Raw -LiteralPath (Join-Path $repositoryRoot 'chris-command.php')
if ($header -notmatch '(?m)^ \* Version:\s+([0-9]+\.[0-9]+\.[0-9]+)\s*$') {
    throw 'Unable to read the plugin version header.'
}
if ($Matches[1] -ne $Version) {
    throw "Requested version $Version does not match plugin header $($Matches[1])."
}

New-Item -ItemType Directory -Path $distPath -Force | Out-Null
Remove-Item -LiteralPath $archivePath -Force -ErrorAction SilentlyContinue
Remove-Item -LiteralPath $checksumPath -Force -ErrorAction SilentlyContinue

git -C $repositoryRoot archive --format=zip --prefix=chris-command/ --output=$archivePath HEAD
if ($LASTEXITCODE -ne 0) {
    throw 'git archive failed.'
}

$hash = (Get-FileHash -Algorithm SHA256 -LiteralPath $archivePath).Hash.ToLowerInvariant()
Set-Content -LiteralPath $checksumPath -Value "$hash  chris-command-$Version.zip" -Encoding utf8NoBOM

Write-Output $archivePath
Write-Output $checksumPath
