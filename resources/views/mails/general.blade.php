@component('mail::message')
{{$content}}

<em>
Regards,<br>
{{ config('app.company')['name'] }},<br>
{{ config('app.company')['website'] }},<br>
<p>We train and develop Business Softwares,websites,ERP Systems,Mobile Applications and much more.</p>
</em>
@endcomponent
