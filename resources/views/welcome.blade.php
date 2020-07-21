@extends('layouts.app')

@section('content')
    <section class="hero py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="content col-12 col-md-6 text-center mb-5 mb-md-0">
                    <h3 class="h3 text-uppercase text-primary">Manage your tasks from one place</h3>
                    <p class="text-info">Portal Desk is a Free Assignment Management Solution for Freelancers</p>
                    <a href="{{ route('register') }}" class="btn btn-primary">Register Today</a>
                </div>
                <div class="hero-img col-md-6 col-12">
                    <img src="./img/At work.svg" title="https://www.freepik.com/free-photos-vectors/computer"
                         alt="Hero image" class="w-100">
                </div>
            </div>
        </div>
    </section>
    <section class="features bg-dark text-light py-5">
        <div class="container">
            <div class="row text-center justify-content-between">
                <div class="col-12 col-md-4">
                    <div class="icon_container" style="font-size: 2.75rem">
                        <i class="fas fa-paste"></i>
                    </div>
                    <h4 class="h4">
                        Manage Unlimited Assignments
                    </h4>
                </div>
                <div class="col-12 col-md-4 my-md-0 my-2">
                    <div class="icon_container" style="font-size: 2.75rem">
                        <i class="fas fa-stopwatch"></i>
                    </div>
                    <h4 class="h4">
                        Easy Deadline Management
                    </h4>
                </div>
                <div class="col-12 col-md-4 my-md-0 my-2">
                    <div class="icon_container" style="font-size: 2.75rem">
                        <i class="fas fa-comment-alt"></i>
                    </div>
                    <h4 class="h4">
                        Inbuilt Discussion Forum
                    </h4>
                </div>
            </div>
        </div>
    </section>
@endsection
