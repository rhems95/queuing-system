# Clear Chrome kiosk profile sticky printer so silent print uses Windows default.
param(
    [Parameter(Mandatory = $true)]
    [string]$ProfileDir,

    [string]$PreferredPrinter = 'XP-58(XP-Q90EC)'
)

$ErrorActionPreference = 'Stop'

$prefPath = Join-Path $ProfileDir 'Default\Preferences'
if (-not (Test-Path $prefPath)) {
    Write-Host 'No Chrome Preferences yet (fresh profile).'
    exit 0
}

try {
    $raw = Get-Content -Path $prefPath -Raw -Encoding UTF8
    $json = $raw | ConvertFrom-Json
} catch {
    Write-Host 'Could not parse Chrome Preferences; leaving as-is.'
    exit 0
}

$changed = $false

if ($null -ne $json.print_preview_sticky_settings) {
    $json.PSObject.Properties.Remove('print_preview_sticky_settings')
    $changed = $true
}

if ($null -ne $json.printing) {
    foreach ($name in @('print_preview_sticky_settings', 'selectedDestinationId', 'destination_id')) {
        if ($json.printing.PSObject.Properties.Name -contains $name) {
            $json.printing.PSObject.Properties.Remove($name)
            $changed = $true
        }
    }
}

if (-not $changed) {
    Write-Host 'Chrome printer sticky settings already clean.'
    exit 0
}

$out = $json | ConvertTo-Json -Depth 100 -Compress
Set-Content -Path $prefPath -Value $out -Encoding UTF8
Write-Host ("Cleared Chrome sticky printer settings. Preferred: " + $PreferredPrinter)
exit 0
