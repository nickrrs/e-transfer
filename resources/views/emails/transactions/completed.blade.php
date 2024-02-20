<x-mail::message>
# Introduction

Você recebeu uma transferência bancária, no valor de R${{$transaction->amount}} pelo E-transfer.

<x-mail::button :url="''">
Verificar Transferência
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
