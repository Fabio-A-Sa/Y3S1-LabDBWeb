@include('partials.alert')
    <section class="login-form-sidebar">
        <h3 class="group-div-title"> Log in </h3>
        <form id="login-form" method="POST"  class="login-form-sidebar-action" action="{{ route('login') }}">
            {{ csrf_field() }}

            <label for="email">E-mail</label>
            <input placeholder="Email" class="input-text-field" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @if ($errors->has('email'))
                <span class="error">
                {{ $errors->first('email') }}
                </span>
            @endif

            <label for="password" >Password</label>
            <input placeholder="Password" class="input-text-field" id="password" type="password" name="password" required>
            @if ($errors->has('password'))
                <span class="error">
                    {{ $errors->first('password') }}
                </span>
            @endif

            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
            </label>

            <section class="sidebar-login-form-buttons">
                <button type="submit" class="sidebar-button-2">
                    <i class="fa fa-sign-in" aria-hidden="true"></i> Login
                </button>
                <button type="button" class="sidebar-button-2" onclick="window.location='{{ route("register") }}'">
                    <i class="fa fa-plus" aria-hidden="true"></i> Register
                </button>
            </section>
        </form>
    </section>
</header>
<section id="bar-middle">
    <section class="recover-password-sidebar">
        <h3 class="group-div-title">Password Recovery</h3>
        @if(!(Session::has('invalid_token') || Session::has('match_error') || Session::has('size_error')))
            <button class="sidebar-button-2" id="recoverButton" onclick="showRecoverPassword()">Recover my password</button>
        @endif
        <section id="recover" class="sidebar-recover" hidden>
            <h5>Please insert your email. A validation token will be sent to set a new password.</h5>
            <form>
                <label for="recoverEmail">E-mail</label>
                <input placeholder="Your email" class="input-text-field" id="recoverEmail" type="email" name="recoverEmail" required autofocus>
                <section class="sidebar-recover-form-buttons">
                    <button class="sidebar-button-2" type="button" onclick=recoverPassword()> 
                        <i class="fa fa-envelope" aria-hidden="true"></i> Send email
                    </button>
                </section>
            </form>
        </section>
        <section id="recoverPassword" class="sidebar-revoer-password" {{(Session::has('invalid_token') || Session::has('match_error') || Session::has('size_error')) ? '' : 'hidden'}}>
            <h5>If the email exists in our database, a validation token was sent. Please enter your token and new password:</h5>
            <form action="{{ route('recoverPassword') }}" method="POST">
                {{ csrf_field() }}
                <input id="recoverAttemp" name="recoverAttemp" value="{{Session::has('email_attemp') ? session('email_attemp') : ''}}" hidden>
                <label for="recoverToken">Token</label>
                <input placeholder="Token" class="input-text-field" id="recoverToken" type="text" name="recoverToken" required autofocus>
                @if (Session::has('invalid_token'))
                    <span class="error">
                    {{ session('invalid_token') }}
                    </span>
                @endif
                <label for="recoverPassword1">Password</label>
                <input placeholder="Password" class="input-text-field" id="recoverPassword1" type="password" name="recoverPassword1" required autofocus>
                <label for="recoverPassword2">Verify password</label>
                <input  placeholder="Confirm your password" class="input-text-field" id="recoverPassword2" type="password" name="recoverPassword2" required autofocus>
                @if (Session::has('match_error'))
                    <span class="error">
                    {{ session('match_error') }}
                    </span>
                @endif
                @if (Session::has('size_error'))
                    <span class="error">
                    {{ session('size_error') }}
                    </span>
                @endif
                <section class="sidebar-recover-form-buttons">
                    <button class="sidebar-button-2" type="submit">Recover</button>
                </section>
            </form>
        </section>
    </section>
</section>