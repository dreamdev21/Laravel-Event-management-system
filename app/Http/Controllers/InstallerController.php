<?php

namespace App\Http\Controllers;

use App\Models\Timezone;
use Artisan;
use Config;
use DB;
use Illuminate\Http\Request;

class InstallerController extends Controller
{

    /**
     * InstallerController constructor.
     */
    public function __construct()
    {
        /**
         * If we're already installed kill the request
         * @todo Check if DB is installed etc.
         */
        if (file_exists(base_path('installed'))) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Show the application installer
     *
     * @return mixed
     */
    public function showInstaller()
    {
        /*
         * Path we need to make sure are writable
         */
        $data['paths'] = [
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            public_path(config('attendize.event_images_path')),
            public_path(config('attendize.organiser_images_path')),
            public_path(config('attendize.event_pdf_tickets_path')),
            base_path('bootstrap/cache'),
            base_path('.env'),
            base_path(),
        ];

        /*
         * Required PHP extensions
         */
        $data['requirements'] = [
            'openssl',
            'pdo',
            'mbstring',
            'fileinfo',
            'tokenizer',
            'gd',
            'zip',
        ];

        /*
         * Optional PHP extensions
         */
        $data['optional_requirements'] = [
            'pdo_pgsql',
            'pdo_mysql',
        ];

        return view('Installer.Installer', $data);
    }

    /**
     * Attempts to install the system
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postInstaller(Request $request)
    {
        set_time_limit(300);

        $database['type'] = $request->get('database_type');
        $database['host'] = $request->get('database_host');
        $database['name'] = $request->get('database_name');
        $database['username'] = $request->get('database_username');
        $database['password'] = $request->get('database_password');

        $mail['driver'] = $request->get('mail_driver');
        $mail['port'] = $request->get('mail_port');
        $mail['username'] = $request->get('mail_username');
        $mail['password'] = $request->get('mail_password');
        $mail['encryption'] = $request->get('mail_encryption');
        $mail['from_address'] = $request->get('mail_from_address');
        $mail['from_name'] = $request->get('mail_from_name');
        $mail['host'] = $request->get('mail_host');

        $app_url = $request->get('app_url');
        $app_key = str_random(16);
        $version = file_get_contents(base_path('VERSION'));

        if ($request->get('test') === 'db') {
            $is_db_valid = self::testDatabase($database);

            if ($is_db_valid === 'yes') {
                return [
                    'status'  => 'success',
                    'message' => 'Success, Your connection works!',
                    'test'    => 1,
                ];
            }

            return [
                'status'  => 'error',
                'message' => 'Unable to connect! Please check your settings',
                'test'    => 1,
            ];
        }

        $config = "APP_ENV=production\n" .
            "APP_DEBUG=false\n" .
            "APP_URL={$app_url}\n" .
            "APP_KEY={$app_key}\n" .
            "DB_TYPE={$database['type']}\n" .
            "DB_HOST={$database['host']}\n" .
            "DB_DATABASE={$database['name']}\n" .
            "DB_USERNAME={$database['username']}\n" .
            "DB_PASSWORD={$database['password']}\n\n" .
            "MAIL_DRIVER={$mail['driver']}\n" .
            "MAIL_PORT={$mail['port']}\n" .
            "MAIL_ENCRYPTION={$mail['encryption']}\n" .
            "MAIL_HOST={$mail['host']}\n" .
            "MAIL_USERNAME={$mail['username']}\n" .
            "MAIL_FROM_NAME=\"{$mail['from_name']}\"\n" .
            "MAIL_FROM_ADDRESS={$mail['from_address']}\n" .
            "WKHTML2PDF_BIN_FILE=wkhtmltopdf-amd64\n" .
            "MAIL_PASSWORD={$mail['password']}\n\n";

        $fp = fopen(base_path() . '/.env', 'w');
        fwrite($fp, $config);
        fclose($fp);

        Config::set('database.default', $database['type']);
        Config::set("database.connections.{$database['type']}.host", $database['host']);
        Config::set("database.connections.{$database['type']}.database", $database['name']);
        Config::set("database.connections.{$database['type']}.username", $database['username']);
        Config::set("database.connections.{$database['type']}.password", $database['password']);

        DB::reconnect();

        //force laravel to regenerate a new key (see key:generate sources)
        Config::set('app.key', $app_key);
        Artisan::call('key:generate');
        Artisan::call('migrate', ['--force' => true]);
        if (Timezone::count() == 0) {
            Artisan::call('db:seed', ['--force' => true]);
        }
        Artisan::call('optimize', ['--force' => true]);

        $fp = fopen(base_path() . '/installed', 'w');
        fwrite($fp, $version);
        fclose($fp);

        return redirect()->route('showSignup', ['first_run' => 'yup']);
    }

    private function testDatabase($database)
    {
        Config::set('database.default', $database['type']);
        Config::set("database.connections.{$database['type']}.host", $database['host']);
        Config::set("database.connections.{$database['type']}.database", $database['name']);
        Config::set("database.connections.{$database['type']}.username", $database['username']);
        Config::set("database.connections.{$database['type']}.password", $database['password']);

        try {
            DB::reconnect();
            $success = DB::connection()->getDatabaseName() ? 'yes' : 'no';
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $success;
    }
}
