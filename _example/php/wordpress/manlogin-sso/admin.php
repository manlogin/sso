<?php
if (!is_admin()) {
    die();
}
?><div class="wrap">
    <h2>ورود یکپارچه من لاگین</h2>
    <form method="post" action="options.php">
        <?php echo settings_fields( 'manlogin_sso' ); ?>
        <p>
            برای ایجاد کلیدهای جدید <a href="https://manlogin.com/panel/#/developers/apps" target="_blank">اینجا</a>
            کلیک کنید.
        </p>
        <table class="form-table form-v2">
            <tr valign="top">
                <th scope="row"><label for="manLogin_sso_uid">شناسه یکتا: </span>
                    </label></th>
                <td><input type="text" id="manLogin_sso_uid" name="manLogin_sso_uid"
                        value="<?php echo get_option('manLogin_sso_uid'); ?>" size="30" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="manLogin_sso_publicKey">کلید عمومی: (<a target="_blank"
                            href="https://manlogin.com/fa/doc/sso#publicKey">؟</a>)</span>
                    </label></th>
                <td><input type="text" id="manLogin_sso_publicKey" name="manLogin_sso_publicKey"
                        value="<?php echo get_option('manLogin_sso_publicKey'); ?>" size="65" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="manLogin_sso_S2SToken">کلید ارتباط سرور به سرور: (<a target="_blank"
                            href="https://manlogin.com/fa/doc/sso#S2SToken">؟</a>)</span>
                    </label></th>
                <td><input type="text" id="manLogin_sso_S2SToken" name="manLogin_sso_S2SToken"
                        value="<?php echo get_option('manLogin_sso_S2SToken'); ?>" size="65" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="manLogin_sso_token">کلید ساختن هش ریکوئست: (<a target="_blank"
                            href="https://manlogin.com/fa/doc/sso#token">؟</a>)</span>
                    </label></th>
                <td><input type="text" id="manLogin_sso_token" name="manLogin_sso_token"
                        value="<?php echo get_option('manLogin_sso_token'); ?>" size="65" /></td>
            </tr>
        </table>
        <p>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="ذخیره تغییرات">
            <button name="reset" id="reset" class="button">
                حذف شناسه و کلید ها و غیر فعال کردن ورود یکپارچه
            </button>
        </p>
    </form>
</div>
<script>
    (function ($) {
        $('#reset').on('click', function (e) {
            e.preventDefault();
            $('#manLogin_sso_publicKey').val('');
            $('#manLogin_sso_uid').val('');
            $('#manLogin_sso_S2SToken').val('');
            $('#manLogin_sso_token').val('');
            $('#submit').trigger('click');
        });
    })(jQuery);
</script>
<style>
    #submit+#reset {
        margin-left: 1em;
    }
</style>