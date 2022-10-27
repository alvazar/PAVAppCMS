<div class="outerFormAuth">
    <div style="width: 300px;">
        <div class="title">Авторизация</div>
        <div>
            <form action="/auth" method="post">
                <input type="hidden" name="urlFrom" value="<?= $_SERVER['REQUEST_URI'] ?>" />
                <label>Логин</label>
                <input name="login" type="text" name="login" />
                <label>Пароль</label>
                <input name="passw" type="password" name="passw" />
                <button type="submit">Войти</button>
            </form>
        </div>
    </div>
</div>