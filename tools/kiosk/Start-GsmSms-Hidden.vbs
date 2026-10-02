Option Explicit
' Start Watch-GsmSms.ps1 with no console.

Dim fso, wmi, here, root, watch, outDir, cmd, proc, startup, pid
Set fso = CreateObject("Scripting.FileSystemObject")
Set wmi = GetObject("winmgmts:\\.\root\cimv2")

here = fso.GetParentFolderName(WScript.ScriptFullName)
root = fso.GetParentFolderName(fso.GetParentFolderName(here))
watch = fso.BuildPath(here, "Watch-GsmSms.ps1")
outDir = fso.BuildPath(root, "storage\app\sms-outbox")

If Not fso.FileExists(watch) Then
    WScript.Quit 1
End If

If Not fso.FolderExists(outDir) Then
    fso.CreateFolder outDir
End If

For Each proc In wmi.ExecQuery("SELECT ProcessId, CommandLine FROM Win32_Process WHERE Name='powershell.exe' OR Name='pwsh.exe'")
    If Not IsNull(proc.CommandLine) Then
        If InStr(1, proc.CommandLine, "Watch-GsmSms.ps1", vbTextCompare) > 0 Then
            proc.Terminate
        End If
    End If
Next

cmd = """" & shPowerShell() & """ -NoProfile -NonInteractive -ExecutionPolicy Bypass -File """ & watch & """ -OutDir """ & outDir & """"
Set startup = wmi.Get("Win32_ProcessStartup").SpawnInstance_
startup.ShowWindow = 0
wmi.Get("Win32_Process").Create cmd, Null, startup, pid
WScript.Quit 0

Function shPowerShell()
    Dim sys
    sys = fso.BuildPath(fso.GetSpecialFolder(1), "WindowsPowerShell\v1.0\powershell.exe")
    If fso.FileExists(sys) Then
        shPowerShell = sys
    Else
        shPowerShell = "powershell.exe"
    End If
End Function
