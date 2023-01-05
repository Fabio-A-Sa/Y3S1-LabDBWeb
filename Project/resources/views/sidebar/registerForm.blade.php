<section class="register-form-sidebar">
    <h3 class="group-div-title"> Register </h3>
    <form method="POST" class ="register-form-sidebar-action" action="{{ route('register') }}">
        {{ csrf_field() }}

        <label for="username">Username</label>
        <input placeholder="Username" class="input-text-field" id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
        @if ($errors->has('username'))
            <span class="error">
                {{ $errors->first('username') }}
            </span>
        @endif

        <label for="name">Name</label>
        <input placeholder="Name" class="input-text-field" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
        @if ($errors->has('name'))
            <span class="error">
                {{ $errors->first('name') }}
            </span>
        @endif

        <label for="email">E-Mail Address</label>
        <input placeholder="Email" class="input-text-field" id="email" type="email" name="email" value="{{ old('email') }}" required>
        @if ($errors->has('email'))
            <span class="error">
                {{ $errors->first('email') }}
            </span>
        @endif

        <label for="password">Password</label>
        <input placeholder="Password" class="input-text-field" id="password" type="password" name="password" required>
        @if ($errors->has('password'))
            <span class="error">
                {{ $errors->first('password') }}
            </span>
        @endif

        <label for="password-confirm">Confirm Password</label>
        <input placeholder="Confirm password" class="input-text-field" id="password-confirm" type="password" name="password_confirmation" required>
        <section class="sidebar-register-form-buttons">
            <button type="submit" class="sidebar-button-2">
                Register
            </button>
            <button type="button" class="sidebar-button-2" onclick="window.location='{{ route("login") }}'"> Go to Login</a>
        </section>
    </form>
</section>
</header>