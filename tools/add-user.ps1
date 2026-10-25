# เพิ่ม / เปลี่ยนรหัส / ลบ บัญชี Hyamax Academy ใน academy-users.php
#
#   เพิ่มหรือเปลี่ยนรหัส:  powershell -ExecutionPolicy Bypass -File tools\add-user.ps1 -Username drsomchai -Name "Dr. Somchai"
#   ลบ:                  powershell -ExecutionPolicy Bypass -File tools\add-user.ps1 -Username drsomchai -Remove
#   ดูรายชื่อ:             powershell -ExecutionPolicy Bypass -File tools\add-user.ps1 -List
#
# รหัสผ่านพิมพ์ในช่องที่ซ่อนตัวอักษร และเก็บเป็น hash เท่านั้น — แก้เสร็จแล้วอัปโหลด academy-users.php ขึ้นโฮสต์

param(
    [string]$Username,
    [string]$Name,
    [switch]$Remove,
    [switch]$List
)

$ErrorActionPreference = 'Stop'
$usersFile = Join-Path (Split-Path $PSScriptRoot -Parent) 'academy-users.php'
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
$lines = [System.Collections.Generic.List[string]]([IO.File]::ReadAllLines($usersFile, $utf8NoBom))
$userLine = '^\s*''([^'']+)''\s*=>\s*\['

if ($List) {
    foreach ($l in $lines) { if ($l -match $userLine) { $Matches[1] } }
    exit 0
}

if ($Username -notmatch '^[A-Za-z0-9._-]{3,40}$') {
    throw 'Username ต้องเป็น a-z, 0-9, จุด, ขีด หรือ _ ยาว 3-40 ตัว'
}

$existing = -1
for ($i = 0; $i -lt $lines.Count; $i++) {
    if ($lines[$i] -match $userLine -and $Matches[1] -eq $Username) { $existing = $i }
}

if ($Remove) {
    if ($existing -lt 0) { throw "ไม่พบผู้ใช้ $Username" }
    $lines.RemoveAt($existing)
    [IO.File]::WriteAllLines($usersFile, $lines, $utf8NoBom)
    "ลบ $Username แล้ว — อัปโหลด academy-users.php ขึ้นโฮสต์เพื่อให้มีผล"
    exit 0
}

if (-not $Name) { $Name = $Username }

$p1 = Read-Host "รหัสผ่านของ $Username (อย่างน้อย 10 ตัว)" -AsSecureString
$p2 = Read-Host 'พิมพ์รหัสผ่านอีกครั้ง' -AsSecureString
$plain1 = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($p1))
$plain2 = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($p2))
if ($plain1 -cne $plain2) { throw 'รหัสผ่านสองครั้งไม่ตรงกัน' }
if ($plain1.Length -lt 10) { throw 'รหัสผ่านต้องยาวอย่างน้อย 10 ตัว' }

$salt = New-Object byte[] 16
[Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($salt)
$kdf = New-Object Security.Cryptography.Rfc2898DeriveBytes ([Text.Encoding]::UTF8.GetBytes($plain1)), $salt, 100000, ([Security.Cryptography.HashAlgorithmName]::SHA256)
$hash = -join ($kdf.GetBytes(32) | ForEach-Object { $_.ToString('x2') })
$saltHex = -join ($salt | ForEach-Object { $_.ToString('x2') })
$plain1 = $null; $plain2 = $null

$safeName = $Name.Replace('\', '\\').Replace("'", "\'")
$entry = "    '$Username' => ['name' => '$safeName', 'salt' => '$saltHex', 'hash' => '$hash'],"

if ($existing -ge 0) {
    $lines[$existing] = $entry
    $msg = "เปลี่ยนรหัสผ่านของ $Username แล้ว"
} else {
    $close = $lines.FindLastIndex([Predicate[string]]{ param($l) $l -match '^\];\s*$' })
    if ($close -lt 0) { throw 'หาจุดปิดรายการ ]; ใน academy-users.php ไม่เจอ' }
    $lines.Insert($close, $entry)
    $msg = "เพิ่ม $Username แล้ว"
}

[IO.File]::WriteAllLines($usersFile, $lines, $utf8NoBom)
"$msg — อัปโหลด academy-users.php ขึ้นโฮสต์เพื่อให้มีผล"
