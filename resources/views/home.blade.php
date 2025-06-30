@extends('layouts.app')

@section('title', 'Home')

@section('styles')
<style>
    body {
        background-color: #f2f0ea;
        margin: 0;
        padding: 0;
    }

    .mobile-container {
        width: 100%;
        max-width: 420px;
        margin: 0 auto;
        border-radius: 20px;
        overflow: hidden;
        font-family: 'Arial', sans-serif;
        background-color: white;
        min-height: 100vh;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        position: relative;
    }

    .header {
        background-color: #192b5d;
        color: white;
        padding: 1.5rem;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
    }

    .header .user-name {
        font-weight: bold;
    }

    .header .app-name {
        font-style: italic;
        font-weight: bold;
    }

    .search-box input {
        width: 100%;
        padding: 0.6rem 1rem;
        border-radius: 20px;
        border: none;
        margin-top: 1rem;
        font-size: 0.9rem;
    }

    .e-wallet {
        background-color: white;
        margin: -20px auto 1rem auto;
        border-radius: 12px;
        padding: 1rem;
        width: 90%;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: bold;
    }

    .menu-icons {
        display: flex;
        justify-content: space-around;
        padding: 1rem;
        text-align: center;
    }

    .menu-icons div {
        flex: 1;
    }

    .promo-section {
        padding: 0 1rem;
        font-weight: bold;
    }

    .promo-box {
        background-color: #ddd;
        height: 80px;
        border-radius: 10px;
        margin: 0.5rem 0;
    }

    .bottom-nav {
        display: flex;
        justify-content: space-around;
        background-color: #192b5d;
        padding: 0.8rem 0;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        max-width: 420px;
        margin: 0 auto;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .bottom-nav div {
        text-align: center;
        flex: 1;
    }

    .bottom-nav i {
        color: white;
        font-size: 1.2rem;
    }

    .bottom-nav span {
        font-size: 0.75rem;
        color: white;
        display: block;
    }

    @media (min-width: 768px) {
        .mobile-container {
            margin-top: 2rem;
        }
    }
</style>
@endsection


@section('content')
<div class="mobile-container">
    <div class="header">
        <p>Hello, Selamat Datang di <span class="app-name">M-BJEK</span><br>
        <span class="user-name"> {{ Auth::user()->username }}!</h2>
        <div class="search-box">
            <input type="text" placeholder="Search anything you want...">
        </div>
    </div>

    <div class="e-wallet">
        <div>
        <i class="fas fa-wallet"></i> E-WALLET<br>
            Rp. 500.000
        </div>
        <i class="bi bi-three-dots-vertical"></i>
    </div>

    <div class="menu-icons">
        <div>
            <i class="bi bi-motorbike"></i><br>
            <span>Motor</span>
        </div>
        <div>
            <i class="bi bi-car-front"></i><br>
            <span>Mobil</span>
        </div>
    </div>

    <div class="promo-section">
        AYO SERBU PROMO NYA!
        <div class="promo-box"></div>
        <div class="promo-box"></div>
    </div>
</div>

<div class="bottom-nav">
    <div>
        <i class="bi bi-house-door-fill"></i>
        <span>Beranda</span>
    </div>
    <div>
        <i class="bi bi-clock-history"></i>
        <span>Aktivitas</span>
    </div>
    <div>
        <i class="bi bi-gear"></i>
        <span>Help</span>
    </div>
</div>
@endsection