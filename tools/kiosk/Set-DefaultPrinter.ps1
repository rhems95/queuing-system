# Set Windows default printer by exact name, then by name match.
param(
    [string]$Exact = '',
    [string]$Match = 'XP-Q90EC'
)

$ErrorActionPreference = 'Stop'

$printers = @(Get-CimInstance Win32_Printer)
$p = $null

if ($Exact -and $Exact.Trim() -ne '') {
    $p = $printers | Where-Object { $_.Name -eq $Exact.Trim() } | Select-Object -First 1
}

if (-not $p -and $Match -and $Match.Trim() -ne '') {
    $p = $printers | Where-Object { $_.Name -like ("*" + $Match.Trim() + "*") } | Select-Object -First 1
}

if (-not $p) {
    Write-Host ("ERROR: No printer found matching '" + $Match + "'")
    Write-Host 'Installed printers:'
    foreach ($item in $printers) {
        Write-Host (" - " + $item.Name + "  default=" + $item.Default + "  offline=" + $item.WorkOffline)
    }
    exit 1
}

if ($p.WorkOffline) {
    try {
        $p.WorkOffline = $false
        $p.Put() | Out-Null
    } catch {
        # Best-effort only.
    }
}

Invoke-CimMethod -InputObject $p -MethodName SetDefaultPrinter | Out-Null

$check = Get-CimInstance Win32_Printer | Where-Object { $_.Default } | Select-Object -First 1
if (-not $check -or $check.Name -ne $p.Name) {
    Write-Host ("ERROR: Failed to make default: " + $p.Name)
    exit 1
}

Write-Host ("Default printer set to: " + $check.Name)
exit 0
