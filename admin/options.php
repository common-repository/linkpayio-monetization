<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
    <div class='wrap'>
        <h2>LinkPay.io Monetization</h2>
        <form action=<?php echo $action_url ?>><?php wp_nonce_field('pay_nonce_action','pay_nonce_field'); ?>
		<input name="submitted" type="hidden" value="1">
            <table class="form-table">
                <tr>
                    <th>
                        <p>LinkPay.io API Key</p>
                    </th>
                    <td><input name='service_key' type='text' value="<?php echo get_option('pay_api_key') ?>"></td>
                </tr>
                <tr>
                    <td><?php submit_button(); ?></td>
                    <td></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>