<form class="verify-gcaptcha" action="{{ route('user.login') }}" method="POST">
    @csrf
    <div class="form-group">
        <label class="form-label">@lang('Username or Email')</label>
        <input class="form-control form--control" name="username" type="text" value="{{ old('username') }}" required>
    </div>
    <div class="form-group">
        <label class="form-label">@lang('Password')</label>
        <div class="input-group input--group">
            <input class="form-control form--control" name="password" type="password" required>
            <span class="input-group-text pass-toggle">
                <i class="las la-eye"></i>
            </span>
        </div>
    </div>

    <x-captcha />

    <div class="form-group d-flex justify-content-between align-items-center">
        <div class="form-check">
            <input class="form-check-input custom--check" id="remember" name="remember" type="checkbox" @checked(old('remember'))>
            <label class="form-check-label sm-text t-heading-font heading-clr fw-md" for="remember">
                @lang('Remember Me')
            </label>
        </div>
        <a class="t-link--base sm-text" href="{{ route('user.password.request') }}">@lang('Forgot Password?')</a>
    </div>

    <button class="btn btn--xl btn--base w-100" type="submit">@lang('Login')</button>
    
    @if (gs('registration'))
        <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
            <span class="d-inline-block sm-text"> @lang('Don\'t have account?') </span>
            <a class="t-link d-inline-block t-link--base base-clr sm-text lh-1 text-center text-end" href="{{ route('user.register') }}">@lang('Create account')</a>
        </div>
    @endif
</form>
