$ErrorActionPreference = "Stop"

git checkout master
Remove-Item -Recurse -Force public/build -ErrorAction SilentlyContinue
pnpm build

git checkout -B deploy
git add -A
git add -f public/build

git commit -m "Deploy build $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" || Write-Host "Tidak ada perubahan untuk commit."

git push -f gitbps deploy

git checkout master

Write-Host "Selesai deploy ke Git BPS branch deploy."
