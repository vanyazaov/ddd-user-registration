@extends('web::layouts.app')

@section('title', 'Регистрация')

@section('content')
<h1>Регистрация</h1>

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div>
        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email') <p style="color:red">{{ $message }}</p> @enderror
    </div>
    <div>
        <label>Пароль:</label>
        <input type="password" name="password">
        @error('password') <p style="color:red">{{ $message }}</p> @enderror
    </div>
    <button type="submit">Зарегистрироваться</button>
</form>
@endsection
