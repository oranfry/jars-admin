<div class="login-page">
    <h1 style="margin-bottom: 1em;">Jars Admin</h1>

    <div class="panel-choosers">
        <div class="panel-chooser current">Credentials</div>
        <div class="panel-chooser">Token</div>
    </div>

    <div class="panels">
        <div class="panel">
            <form id="loginform">
                <div class="cred-line">
                    <p>Username</p>
                    <input type="text" name="username" id="auth" autocomplete="current-password" value="<?= @$username ?>">
                </div>
                <div class="cred-line">
                    <p>Password</p>
                    <div class="showpassword">🙈</div>
                    <input type="password" name="password" id="password">
                </div>
                <div class="cred-line">
                    <input type="submit" value="Sign In">
                </div>
            </form>
        </div>
        <div class="panel" style="display: none">
            <form id="tokenform">
                <div class="cred-line">
                    <p>Token</p>
                    <div class="showpassword">🙈</div>
                    <input type="password" name="token" id="token" autocomplete="current-password">
                </div>
                <div class="cred-line">
                    <input type="submit" value="Apply">
                </div>
            </form>
        </div>
    </div>
</div>

<script>document.getElementById('<?= @$username ? 'password' : 'auth' ?>').focus();</script>
