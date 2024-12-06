<div class="container">
    <form class="form" id="login">
        <h1 class="form__title">Login</h1>
        <div class="form__input-group">
            <input type="text" class="form__input" name="username" placeholder="Username or email" required>
        </div>
        <div class="form__input-group">
            <input type="password" class="form__input" name="password" placeholder="Password" required>
        </div>
        <button class="form__button" type="submit">Continue</button>
        <p class="form__text">
            <a href="#" class="form__link" id="linkCreateAccount">Don't have an account? Create account</a>
        </p>
    </form>

    <form class="form form--hidden" id="createAccount">
        <h1 class="form__title">Create Account</h1>
        <div class="form__input-group">
            <input type="text" id="signupUsername" class="form__input" name="signupUsername" placeholder="Username"
                required>
        </div>
        <div class="form__input-group">
            <input type="email" id="signupEmail" class="form__input" name="signupEmail" placeholder="Email Address"
                required>
        </div>
        <div class="form__input-group">
            <input type="password" id="signupPassword" class="form__input" name="signupPassword"
                placeholder="Password" required>
        </div>
        <div class="form__input-group">
            <input type="password" id="signupConfirmPassword" class="form__input" name="signupConfirmPassword"
                placeholder="Confirm Password" required>
        </div>
        <button class="form__button" type="submit">Sign Up</button>
        <p class="form__text">
            <a href="#" class="form__link" id="linkLogin">Already have an account? Sign in</a>
        </p>
    </form>
</div>