@extends('layouts.app')
@include('sidebar.bar')

@section('sidebar')
    @yield('bar')
@endsection

@section('content')
    <div class="help-card">
        <h1>Help / Frequently Asked Questions</h1>
        <section id="questions" class="faq-questions">
            <article class="question">
                <a data-toggle="collapse" href="#answer1" role="button">
                    <h2>+ What is OnlyFEUP?</h2>
                </a>
                <div class="collapse" id="answer1">
                    <p>OnlyFEUP is a web-based social network with the purpose of creating connections between students and staff from FEUP.</p>
                </div>
            </article>        
            
            <article class="question">
                <a data-toggle="collapse" href="#answer2" role="button">
                    <h2>+ I forgot my password, what can I do?</h2>
                </a>
                <div class="collapse" id="answer2">
                    <p>You may click on the button labeled 'Recover my password' on the login page, and follow the instructions there. If you need further help please don't hesitate to contact us.</p>
                </div>
            </article>        

            <article class="question">
                <a data-toggle="collapse" href="#answer3" role="button">
                    <h2>+ How can I delete my account?</h2>
                </a>
                <div class="collapse" id="answer3">
                    <p>In the sidebar on your profile page, you will see a button labeled 'Delete account'. Clicking the button will ask you for a confirmation, if you accept, you account will be deleted. Be careful, this action cannot be reversed!</p>
                </div>
            </article>

            <article class="question">
                <a data-toggle="collapse" href="#answer4" role="button">
                    <h2>+ Why did my post disapear?</h2>
                </a>
                <div class="collapse" id="answer4">
                    <p>You may have posted content that breached our Terms of Service, in which case it may have been deleted by an administrator. If you think this action was not justified, please contact a staff member.</p>
                </div>
            </article>

            <article class="question">
                <a data-toggle="collapse" href="#answer5" role="button">
                    <h2>+ Why was my account blocked?</h2>
                </a>
                <div class="collapse" id="answer5">
                    <p>Some of your actions were deeemed as breaching our Terms of Service, in which case you may have been blocked by an administrator. If you think this action was not justified, please contact a staff member.</p>
                </div>
            </article> 
        </section>
    </div>
    <section class="help-contacts">
        <h1 id="contacts-title">Contacts<h1>
        <ul>
        @foreach ($admins as $admin)
            <li><a href="/user/{{$admin->id}}" class="help-admin-info">{{$admin->name}}</a>: {{$admin->email}}</li>
        @endforeach
        </ul>
        
    </section>
@endsection