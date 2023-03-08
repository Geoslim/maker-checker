@component('mail::message')
Hello <span class="bold">{{ $user['first_name'] }},</span>

<p>Please see below details of the recent request made:</p>

<p>Request details:</p>

| | |
| ---- | ----: |
| Request Type: | {{ $request->type }} |


@if($request->type !== \App\Enums\RequestType::DELETE && !is_null($data))
| | |
| ---- | ----: |
| <small>Data:</small> | |
| First Name | {{ $data['first_name'] }} |
| Last Name | {{ $data['last_name'] }} |
| Email | {{ $data['email'] }} |
@endif


@if($request->type != \App\Enums\RequestType::CREATE && !is_null($request->user))
| | |
| ---- | ----: |
| User Identifier: | {{ $request->user_id }} |
@endif

<br />

@if($request->type !== \App\Enums\RequestType::CREATE && !is_null($request->user))
<p>Current details: </p>

| | |
| ---- | ----: |
| First Name: | {{ $request->user->first_name }} |
| Last Name: | {{ $request->user->last_name }} |
| Email: | {{ $request->user->email }} |

@endif

<br />
<p>Request made by: {{ $request->maker->first_name }} {{ $request->maker->last_name }}</p>

@endcomponent
