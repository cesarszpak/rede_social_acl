<?php
/**
 * Default bootstrap for UserManagement CakePHP plugin.
 *
 * CakePlugin::load('UserManagement', array('bootstrap' => true, 'routes' => true));
 */
//Enter your facebook credentials
Configure::write('facebook-cred',
    array(
        'appId' => '394069217427607',
        'secret' => '7f5ea1db6ccd56e51bcfb395db7d1c6d',
    )
);

//Enter your google credentials
Configure::write('google-cred',
    array(
        'clientID' =>'490529133079-gqlfdsi0mscpufqk5b3i1fchss1o22pm.apps.googleusercontent.com',
        'clientSecret'=>'jDUHKHTwJXpdFJTsPtc4oy98',
        'developerKey'=>'490529133079-gqlfdsi0mscpufqk5b3i1fchss1o22pm@developer.gserviceaccount.com'
    )
);

//Enter your twitter credentials
Configure::write('twitter-cred',
    array(
        'consumer_key' => 'CoURseWWIzZzLmIpg46U55CoQ',
        'consumer_secret' => 'iQafe7NkCk5ulyhF2ADjJi9Ro7Q6kEraTx1mY9JKc3zcDASxO7'
    )
);

//Enter your linkedin credentials
Configure::write('linkedin-cred',
    array(
        'API_KEY' => '75ubkv1kpff9jx',
        'API_SECRET' => 'Vyt6C00a94MmqLAa',
        'SCOPE'=>'r_basicprofile r_emailaddress rw_nus'
    )
);

//group id which you want to assign to those user who register using social icons
Configure::write('socialLoginGroupId',2);
