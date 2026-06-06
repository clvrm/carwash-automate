<?php

use app\models\ar\chat\Chat;
use app\models\ar\chat\ChatMessages;

$this->title = 'Чат';

$this->params['main-data'] = [
    'data-log-data' => 'Перешли на страницу чата'
];

/**
 * @var Chat[] $chats
 */
?>
<div class="row">
    <div class="col-lg-5">
        <div class="card card-custom gutter-b">
            <div class="card-body">
                <div class="row">
                    <ul class="chat-dialogs">
                        <a href="/chat/support">
                            <li class="pb-4 chat-dialog--item chat-dialog--support">
                                <img class="mr-1" src="/media/service/chat-support-i.png"> Поддержка
                                <i class="fas fa-chevron-right ml-4"></i>
                            </li>
                        </a>
                        <?php foreach ($chats as $chat): ?>
                            <li class="py-4 d-flex align-items-center justify-content-between chat-dialog--item user-order-chat--item "
                                data-chat-id="<?= $chat->id ?>">
                                <div class="d-flex align-items-center font-weight-bold">
                                    <div class="dialog-item--car-number"><?= $chat->car_number ?? 'номер неизвестен' ?></div>
                                    <div class="mr-1 ml-1 text-muted">|</div>
                                    <div class="dialog-item--car-region"><?= $chat->car_region ?? '--' ?></div>
                                </div>
                                <div class="d-block">
                                    <?php
                                    $lastChatMessages = ChatMessages::find()->where(['chat_id' => $chat->id])->orderBy('created_at')->one();
                                    $countNewMessages = ChatMessages::find()->where(['chat_id' => $chat->id, 'is_viewed' => ChatMessages::NOT_VIEWED])
                                        ->andWhere(['not', ['client_id' => null]])->count();
                                    ?>
                                    <div class="dialog-item--time"><?= isset($lastChatMessages) ? $lastChatMessages->created_at : '-' ?></div>
                                    <div class="dialog-item--message-count mt-3">
                                        <span class="label label-primary"><?= $countNewMessages ?? 0 ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div id="currentChatCard" class="card card-custom gutter-b" data-chat-id="0" style="display: none">
            <div class="card-header align-items-center justify-content-between flex-wrap">
                <div class="d-flex font-size-h4 align-items-center font-weight-bold">
                    <div class="chat--car-number">o111oo</div>
                    <div class="mr-1 ml-1 text-muted">|</div>
                    <div class="chat--car-region">178</div>
                </div>
                <a id="linkToOrder" target="_blank" href="#">
                    <button class="btn btn-outline-primary">Смотреть заказ</button>
                </a>
            </div>
            <div class="card-body">
                <div class="dialog-chat pr-2" data-mobile-height="350" style="height: 300px;overflow: auto;">
                    <!--begin::Messages-->
                    <div class="messages">
                        <!--begin::Message In-->
                        <div class="d-flex flex-column mb-5 align-items-start">
                            <div class="d-flex align-items-center">
                                <div>
                                    <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">
                                        <div class="d-flex font-size-h4 align-items-center font-weight-bold">
                                            <div class="chat--car-number">o111oo</div>
                                            <div class="mr-1 ml-1 text-muted">|</div>
                                            <div class="chat--car-region">178</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="chat-message chat-message-in mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">
                                <span class="chat-message-time  text-muted font-size-sm">2 Hours</span>

                                How likely are you to recommend our company to your friends and family?
                            </div>
                        </div>
                        <!--end::Message In-->
                        <!--begin::Message Out-->
                        <div class="d-flex flex-column mb-5 align-items-end">
                            <div class="d-flex align-items-center">
                                <div>
                                    <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">
                                        Вы
                                    </a>
                                </div>
                            </div>
                            <div class="chat-message chat-message-out mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">
                                <span class="chat-message-time text-muted font-size-sm">3 minutes</span>

                                Hey there, we’re just writing to let you know that you’ve been subscribed to a
                                repository on GitHub.
                            </div>
                        </div>
                        <!--end::Message Out-->
                        <!--begin::Message In-->
                        <div class="d-flex flex-column mb-5 align-items-start">
                            <div class="d-flex align-items-center">
                                <div>
                                    <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">
                                        <div class="d-flex font-size-h4 align-items-center font-weight-bold">
                                            <div class="chat--car-number">o111oo</div>
                                            <div class="mr-1 ml-1 text-muted">|</div>
                                            <div class="chat--car-region">178</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="chat-message chat-message-in mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">
                                <span class="chat-message-time text-muted font-size-sm">40 seconds</span>
                                Most purchased Business courses during this sale!
                            </div>
                        </div>
                        <!--end::Message In-->
                        <!--begin::Message Out-->

                        <!--end::Message Out-->
                    </div>
                    <!--end::Messages-->
                </div>
            </div>
            <div class="card-footer align-items-center">
                <textarea id="textMessage" class="form-control border-0 p-0" rows="2"
                          placeholder="Ваш текст"></textarea>
                <div class="d-flex align-items-center justify-content-end mt-5">
                    <div>
                        <button id="sendChatMessage" type="button"
                                class="float-right btn btn-primary btn-lg px-10 text-uppercase font-weight-bold chat-send py-2 px-6">
                            Отправить
                            <i class="fas ml-2 fa-sm  pr-0 fa-angle-double-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var chat = $(".dialog-chat");
    chat.scrollTop(chat.prop('scrollHeight'));

    $(document).ready(function () {
        let cwId = $('#mainInfoBlock').data('cw-id');
        let pId = $('#mainInfoBlock').data('p-id');

        $('#sendChatMessage').on('click', function () {
            let text = $('#textMessage').val();
            let chatId = $('#currentChatCard').data('chat-id');

            $.ajax({
                type: 'POST',
                url: '/ajax/chat/send-message',
                data: {chatId: chatId, pId: pId, text: text},
                success: function (data) {
                    if (data.result) {
                        $('#textMessage').val('');
                        getChat(chatId);
                        setTimeout(function () {
                            $('.dialog-chat').scrollTop($('.dialog-chat')[0].scrollHeight);
                        }, 100)
                    } else {
                        toastr.error("Не удалось отправить сообщение, повторите попытку позже");
                    }
                }
            });
        });

        $('.user-order-chat--item').on('click', function () {
            let chatId = $(this).data('chatId');
            $('#textMessage').val('');

            getChat(chatId);
        });


        function getChat(chatId) {
            $.ajax({
                type: 'GET',
                url: '/ajax/chat/get-chat',
                data: {id: chatId, pId: pId},
                success: function (data) {
                    if (!data.result) {
                        toastr.success(data.message);
                        $('#currentChatCard').hide(500);
                    } else {
                        console.log(data);
                        let carNumber = data.chat.car_number;
                        let chatId = data.chat.id;
                        let carRegion = data.chat.car_region;
                        let orderId = data.chat.order_id;

                        $('#linkToOrder').attr('href', '/orders/show?id=' + orderId);
                        $('#currentChatCard').data('chat-id', chatId);
                        $('#currentChatCard .chat--car-number').text(carNumber);
                        $('#currentChatCard .chat--car-region').text(carRegion);

                        $('#currentChatCard .dialog-chat .messages').empty();

                        let messagesHtml = '';
                        $.each(data.messages, function (i, item) {
                            let messageHtml = '';
                            let textMessage = item.text;
                            let isViewed = item.is_viewed;
                            let createdAt = item.created_at;
                            let isClientMessage = false;
                            if (item.client_id > 0) {
                                isClientMessage = true;
                            }

                            if (isClientMessage) {
                                messageHtml = '<div class="d-flex flex-column mb-5 align-items-start">' +
                                    '<div class="d-flex align-items-center"><div>' +
                                    '<a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">' +
                                    '<div class="d-flex font-size-h4 align-items-center font-weight-bold">' +
                                    '<div class="chat--car-number">' + carNumber + '</div>' +
                                    '<div class="mr-1 ml-1 text-muted">|</div>' +
                                    '<div class="chat--car-region">' + carRegion + '</div></div>' +
                                    '</a>' +
                                    '</div></div>' +
                                    '<div class="chat-message chat-message-in mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">' +
                                    '<span class="chat-message-time  text-muted font-size-sm">' + createdAt + '</span>' +
                                    textMessage +
                                    '</div></div>';
                            } else {
                                messageHtml = '<div class="d-flex flex-column mb-5 align-items-end">' +
                                    '<div class="d-flex align-items-center"><div>' +
                                    '<a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">' +
                                    'Вы</a></div></div>' +
                                    '<div class="chat-message chat-message-out mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">' +
                                    '<span class="chat-message-time text-muted font-size-sm">' + createdAt + '</span>' +
                                    textMessage +
                                    '</div></div>';
                            }

                            messagesHtml += messageHtml;
                        });

                        $(messagesHtml).appendTo('#currentChatCard .dialog-chat .messages');
                        $('#currentChatCard').show(200);
                    }
                }
            });
        };
    });
</script>