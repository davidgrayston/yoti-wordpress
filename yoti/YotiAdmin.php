<?php
/**
 * Class YotiAdmin
 *
 * @author Yoti Ltd <sdksupport@yoti.com>
 */
class YotiAdmin
{
    /**
     * @var self
     */
    private static $_instance;

    /**
     * init
     */
    public static function init()
    {
        if (!self::$_instance)
        {
            self::$_instance = new self;

            self::$_instance->options();
        }
    }

    /**
     * singleton
     */
    private function __construct()
    {
    }

    /**
     * singleton
     */
    private function __clone()
    {
    }

    /**
     * options page for admin
     */
    private function options()
    {
        // Make sure user can edit
        if (!current_user_can('manage_options'))
        {
            return;
        }

        // Get current config
        $config = YotiHelper::getConfig();

        // Check curl has preliminary extensions to run
        $errors = array();
        if (!function_exists('curl_version'))
        {
            $errors[] = "PHP module 'curl' not installed. Yoti requires it to work. Please contact your server administrator.";
        }
        if (!function_exists('json_decode'))
        {
            $errors[] = "PHP module 'json' not installed. Yoti requires it to work. Please contact your server administrator.";
        }

        // Get data
        $data = $config;
        $updateMessage = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $data['yoti_app_id'] = $this->postVar('yoti_app_id');
            $data['yoti_scenario_id'] = $this->postVar('yoti_scenario_id');
            $data['yoti_sdk_id'] = $this->postVar('yoti_sdk_id');
            $data['yoti_delete_pem'] = ($this->postVar('yoti_delete_pem')) ? true : false;
            $pemFile = $this->filesVar('yoti_pem', $config['yoti_pem']);
            $data['yoti_only_existing'] = $this->postVar('yoti_only_existing');
            $data['yoti_user_email'] = $this->postVar('yoti_user_email');

            // Validation
            if (!$data['yoti_app_id'])
            {
                $errors['yoti_app_id'] = 'App ID is required.';
            }
            if (!$data['yoti_sdk_id'])
            {
                $errors['yoti_sdk_id'] = 'SDK ID is required.';
            }
            if (empty($pemFile['name']))
            {
                $errors['yoti_pem'] = 'PEM file is required.';
            }
            elseif (!empty($pemFile['tmp_name']) && !openssl_get_privatekey(file_get_contents($pemFile['tmp_name'])))
            {
                $errors['yoti_pem'] = 'PEM file is invalid.';
            }

            // No errors? proceed
            if (!$errors)
            {
                // If pem file uploaded then process
                $name = $pemContents = null;
                if (!empty($pemFile['tmp_name']))
                {
                    $name = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $pemFile['name']);
                    if (!$name)
                    {
                        $name = md5($pemFile['name']) . '.pem';
                    }
                    $pemContents = file_get_contents($pemFile['tmp_name']);
                }
                // If delete not ticked
                elseif (!$data['yoti_delete_pem'])
                {
                    $name = $config['yoti_pem']['name'];
                    $pemContents = $config['yoti_pem']['contents'];
                }

                $data = $config = array(
                    'yoti_app_id' => $data['yoti_app_id'],
                    'yoti_scenario_id' => $data['yoti_scenario_id'],
                    'yoti_sdk_id' => $data['yoti_sdk_id'],
                    'yoti_only_existing' => $data['yoti_only_existing'],
                    'yoti_user_email' => $data['yoti_user_email'],
                    'yoti_pem' => array(
                        'name' => $name,
                        'contents' => $pemContents,
                    ),
                );

                // Save config
                update_option(YotiHelper::YOTI_CONFIG_OPTION_NAME, maybe_serialize($config));
                $updateMessage = 'Settings saved.';
            }
        }

        // Display form with scope
        $form = function () use ($data, $errors, $updateMessage)
        {
            require_once __DIR__ . '/views/admin-options.php';
        };
        $form();
    }

    /**
     * @param $var
     * @param null $default
     * @return null
     */
    protected function postVar($var, $default = null)
    {
        return (array_key_exists($var, $_POST)) ? $_POST[$var] : $default;
    }

    /**
     * @param $var
     * @param null $default
     * @return null
     */
    protected function filesVar($var, $default = null)
    {
        return (array_key_exists($var, $_FILES) && !empty($_FILES[$var]['name'])) ? $_FILES[$var] : $default;
    }
}