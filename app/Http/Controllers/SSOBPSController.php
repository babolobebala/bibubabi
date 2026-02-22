<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JKD\SSO\Client\Provider\Keycloak;

class SSOBPSController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SSOBPSController
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Route Redirect dari SSO BPS.
     * Pengendali route dari aplikasi
     */
    public function ssoBPSRedirect(Request $request)
    {
        if (Auth::check()) {
            // Jika dia sudah login, kembali ke HALAMAN DEPAN
            return redirect()->route('home');
        } else {
            if (! $request->has('code')) {
                // Jika tidak ada parameter SSO, kembali ke HALAMAN LOGIN BIASA
                return redirect()->route('login');
            } else {
                // Jika ada parameter SSO, check parameter itu
                $provider = $this->ssoInstance();
                try {
                    $token = $provider->getAccessToken('authorization_code', [
                        'code' => $_GET['code'],
                    ]);
                } catch (\Exception $e) {
                    exit('Gagal mendapatkan akses token : '.$e->getMessage());
                }

                // Setelah mendapatkan token, ambil data email_bps user untuk login ke aplikasi
                try {

                    $data_sso = $provider->getResourceOwner($token);

                    // Filter Pegawai, cuma boleh 5207
                    $kode_satker = $data_sso->getKodeProvinsi().$data_sso->getKodeKabupaten();

                    if ($kode_satker == '5207') {
                        $user = User::where('email_bps', $data_sso->getEmail())->first();
                        if (! $user) {
                            $user = User::create([
                                'nip' => $data_sso->getNip(),
                                'nip_baru' => $data_sso->getNipBaru(),
                                'username' => $data_sso->getUsername(),
                                'nama' => $data_sso->getName(),
                                'email_bps' => $data_sso->getEmail(),
                                'golongan' => $data_sso->getGolongan(),
                                'jabatan' => $data_sso->getJabatan(),
                                'url_foto' => $data_sso->getUrlFoto(),
                            ]);
                            // TODO INI DI KOMEN SAJA KALAU SUDAH TIDAK PERLU
                        } else {
                            $user->update([
                                'nip' => $data_sso->getNip(),
                                'nip_baru' => $data_sso->getNipBaru(),
                                'username' => $data_sso->getUsername(),
                                'nama' => $data_sso->getName(),
                                'golongan' => $data_sso->getGolongan(),
                                'jabatan' => $data_sso->getJabatan(),
                                'url_foto' => $data_sso->getUrlFoto(),
                            ]);
                        }
                        Auth::login($user);

                        return redirect('/');
                    } else {
                        return redirect('login')->with('bps', 'Maaf, Aplikasi Ini Hanya Untuk Pegawai BPS Kabupaten Sumbawa Barat, Provinsi Nusa Tenggara Barat');
                    }
                } catch (\Exception $e) {
                    exit('Gagal login: '.$e->getMessage());
                }
            }
        }
    }

    /**
     * Redirect ke SSO BPS saat menuju form login
     * Apabila sudah login SSO, akan terautentifikasi via email_bps
     */
    public function ssoBPSLogin(Request $request)
    {
        $provider = $this->ssoInstance();

        if (! isset($_GET['code'])) {
            // Untuk mendapatkan authorization code
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: '.$authUrl);
            exit;

            // Mengecek state yang disimpan saat ini untuk memitigasi serangan CSRF
        } elseif (empty($_GET['state'])
            //      || ($_GET['state'] !== $_SESSION['oauth2state'])
        ) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }
    }

    /**
     * Bypass login, cuma buat di development local!
     * /bypass/?nama=
     */
    public function bypassLogin(Request $request)
    {
        try {
            $username = $_GET['nama'];
            $user = User::where('username', $username)->first();

            if (! $user) {
                exit('Gagal bypass: cihuy');
            }
            Auth::login($user);

            return redirect('/');
        } catch (\Exception $e) {
            exit('Gagal bypass: '.$e->getMessage());
        }
    }

    /**
     * Buat logout ala-ala.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * @return Keycloak
     *
     * Function to access Keycloak - BPS SSO
     */
    private function ssoInstance()
    {
        return new Keycloak([
            'authServerUrl' => env('BPS_AUTH_URL'),
            'realm' => env('BPS_REALM'),
            'clientId' => env('BPS_CLIENT_ID'),
            'clientSecret' => env('BPS_CLIENT_SECRET'),
            'redirectUri' => env('BPS_REDIRECT'),
        ]);
    }
}
