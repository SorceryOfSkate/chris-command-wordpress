[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
$repositoryRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path
$excludedRoots = @('.git', '.tools', 'node_modules', 'vendor', 'dist', 'build', 'coverage')
$selfPath = (Resolve-Path -LiteralPath $PSCommandPath).Path
$failures = [System.Collections.Generic.List[string]]::new()

$files = Get-ChildItem -LiteralPath $repositoryRoot -Recurse -Force -File | Where-Object {
    $relative = [IO.Path]::GetRelativePath($repositoryRoot, $_.FullName).Replace('\', '/')
    -not ($excludedRoots | Where-Object { $relative -eq $_ -or $relative.StartsWith("$_/") })
}

$forbiddenPath = '(?i)(^|/)(work|finances?|mtg|notes?|calendar|customer-data)(/|$)|(^|/)\.env(?:\.|$)|\.(sql|sqlite3?|csv|log|zip|bak)$'
$forbiddenBinary = '(?i)\.(png|jpe?g|gif|webp|pdf|docx|xlsx|pfx|p12|key)$'
$secretPatterns = @(
    '(?i)-----BEGIN (?:RSA |EC |OPENSSH )?PRIVATE KEY-----',
    '(?i)\bAKIA[0-9A-Z]{16}\b',
    '\bgh[pousr]_[A-Za-z0-9_]{30,}\b',
    '\bsk-(?:proj-)?[A-Za-z0-9_-]{20,}\b',
    '(?i)(client_secret|api_key|access_token|private_key)\s*[:=]\s*["''][^"'']{8,}["'']'
)
$privateLocationPatterns = @(
    '(?i)https?://(?:localhost|127\.0\.0\.1|10\.\d+\.\d+\.\d+|192\.168\.\d+\.\d+|172\.(?:1[6-9]|2\d|3[01])\.\d+\.\d+)',
    '(?i)https?://[^\s"'']+/(?:g|project)/[A-Za-z0-9_-]{12,}',
    '(?i)[A-Z]:\\Users\\'
)

foreach ($file in $files) {
    $relative = [IO.Path]::GetRelativePath($repositoryRoot, $file.FullName).Replace('\', '/')

    if ($relative -match $forbiddenPath) {
        $failures.Add("Forbidden public path: $relative")
        continue
    }

    if ($relative -match $forbiddenBinary) {
        $failures.Add("Unapproved binary asset: $relative")
        continue
    }

    if ($file.FullName -eq $selfPath) {
        continue
    }

    $content = Get-Content -Raw -LiteralPath $file.FullName -ErrorAction SilentlyContinue
    if ($null -eq $content) {
        continue
    }

    foreach ($pattern in $secretPatterns + $privateLocationPatterns) {
        if ($content -match $pattern) {
            $failures.Add("Sensitive content pattern in: $relative")
            break
        }
    }
}

if ($failures.Count -gt 0) {
    $failures | Sort-Object -Unique | ForEach-Object { Write-Error $_ }
    exit 1
}

Write-Output "Public-boundary scan passed for $($files.Count) files."
