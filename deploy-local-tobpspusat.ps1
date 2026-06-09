$ErrorActionPreference = 'Stop'

param(
    [string]$Remote = 'gitbps',
    [string]$DeployBranch = 'deploy'
)

function Invoke-CommandChecked {
    param(
        [Parameter(Mandatory = $true)]
        [string]$FilePath,

        [string[]]$Arguments = @()
    )

    & $FilePath @Arguments

    if ($LASTEXITCODE -ne 0) {
        throw "Command failed: $FilePath $($Arguments -join ' ')"
    }
}

$originalBranch = (git rev-parse --abbrev-ref HEAD).Trim()

if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($originalBranch)) {
    throw 'Gagal membaca branch aktif.'
}

$statusBeforeBuild = git status --short

if ($LASTEXITCODE -ne 0) {
    throw 'Gagal membaca status git.'
}

if (-not [string]::IsNullOrWhiteSpace(($statusBeforeBuild | Out-String).Trim())) {
    throw 'Working tree tidak bersih. Commit atau stash perubahan dulu sebelum deploy.'
}

try {
    Invoke-CommandChecked -FilePath 'pnpm' -Arguments @('run', 'build')

    Invoke-CommandChecked -FilePath 'git' -Arguments @('switch', '-C', $DeployBranch)
    Invoke-CommandChecked -FilePath 'git' -Arguments @('add', '-A')
    Invoke-CommandChecked -FilePath 'git' -Arguments @('add', '-f', 'public/build')

    & git diff --cached --quiet --exit-code

    if ($LASTEXITCODE -eq 0) {
        Write-Host 'Tidak ada perubahan untuk deploy.'
        return
    }

    $commitMessage = 'Deploy build {0}' -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss')

    Invoke-CommandChecked -FilePath 'git' -Arguments @('commit', '-m', $commitMessage)
    Invoke-CommandChecked -FilePath 'git' -Arguments @('push', '--force-with-lease', $Remote, $DeployBranch)

    Write-Host "Selesai deploy ke remote '$Remote' branch '$DeployBranch'."
}
finally {
    if (-not [string]::IsNullOrWhiteSpace($originalBranch)) {
        & git switch $originalBranch | Out-Null
    }
}
