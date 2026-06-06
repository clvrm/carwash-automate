<?php

$this->title = 'Подтверждение регистрации';

?>

<div class="row h-100">
    <div class="offset-md-3 col-md-6 col-xs-12 align-self-center">
        <div class="card card-custom card-stretch gutter-b ">
            <!--begin::Header-->
            <div class="mt-8 d-flex align-self-center">
                <h2 class="font-weight-bolder text-dark">Carwash</h2>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-10">
                <div class="text-center font-size-lg">
                    <p class="mb-1">На почту:</p>
                    <div class="mb-1 font-weight-bolder text-primary font-size-h4"><?= $email ?? ' указанную при регистрации ' ?></div>
                    <p>была отправлена ссылка для подтверждения регистрации.</p>
                    <p class="mt-4">Ссылка активна 1 час.</p>
                </div>
            </div>
            <!--end: Card Body-->
        </div>
    </div>
</div>