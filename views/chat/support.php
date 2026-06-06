<?php

use app\models\ar\chat\Chat;
use app\models\ar\chat\ChatMessages;
use app\models\ar\ticket\TicketMessages;
use app\models\ar\ticket\Tickets;

$this->title = 'Чат с тех.поддержкой';


/**
 * @var Tickets[] $tickets
 */
?>
<div class="row">
    <div class="col-lg-5">
        <div class="card card-custom gutter-b">
            <div class="card-body">
                <div class="row">
                    <ul class="chat-dialogs">
                        <li class="pb-4 chat-dialog--item chat-dialog--support d-flex justify-content-between">
                            <?php if (Yii::$app->user->can('perm_respond_reviews')): ?>
                                <a href="/chat">
                                    <div>
                                        <i class="fas fa-chevron-left mr-4" style="float: unset;"></i>
                                        <span>Назад</span>
                                    </div>
                                </a>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createTicket">
                                Создать тикет
                            </button>
                        </li>
                        <?php foreach ($tickets as $ticket): ?>
                            <li class="py-4 d-flex align-items-center justify-content-between chat-dialog--item user-order-chat--item "
                                data-ticket-id="<?= $ticket->id ?>">
                                <div class="d-flex align-items-center font-weight-bold">
                                    <div class="dialog-item--car-number"><?= $ticket->text ?? 'номер неизвестен' ?></div>
                                </div>
                                <div class="d-block">
                                    <?php
                                    $lastChatMessages = TicketMessages::find()->where(['ticket_id' => $ticket->id])->orderBy('created_at')->one();
                                    ?>
                                    <div class="dialog-item--time"><?= isset($lastChatMessages) ? $lastChatMessages->created_at : '-' ?></div>
                                    <div class="dialog-item--message-count mt-3">
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
        <div id="currentChatCard" class="card card-custom gutter-b" data-ticket-id="0" style="display: none">
            <div class="card-header align-items-center justify-content-between flex-wrap">
                <div class="d-flex font-size-h4 align-items-center font-weight-bold">
                    <div class="ticket--text"></div>
                </div>
                <!--                <a class="/">-->
                <!--                    <button class="btn btn-outline-primary">Закрыть тикет</button>-->
                <!--                </a>-->
            </div>
            <div class="card-body">
                <div class="dialog-chat pr-2" data-mobile-height="350" style="height: 300px;overflow: auto;">
                    <!--begin::Messages-->
                    <div class="messages">

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
<div class="modal fade" id="createTicket" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Новый тикет</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Заголовок тикета</label>
                    <input id="ticketTitleInput" class="form-control" type="text" maxlength="64"
                           placeholder="Не пришли уведомления о..."/>
                    <span class="form-text text-muted">Кратко опишите суть проблемы, до 64 символов</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Отменить
                </button>
                <button type="button" id="createNewTicket" class="btn btn-primary font-weight-bold">Создать новый
                    тикет
                </button>
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

        $('#ticketTitleInput').maxlength({
            warningClass: "label label-warning label-rounded label-inline",
            limitReachedClass: "label label-success label-rounded label-inline"
        });

        $('#createNewTicket').on('click', function () {
            let ticketTitle = $('#ticketTitleInput').val();

            $.ajax({
                type: 'POST',
                url: '/ajax/chat/create-ticket',
                data: {title: ticketTitle, pId: pId},
                success: function (data) {
                    if (data.result) {
                        $('#ticketTitleInput').val('');
                        toastr.success("Тикет создан. Страница будет перезагружена");
                        setTimeout(function () {
                            window.location.reload();
                        }, 3000)
                    } else {
                        toastr.error("Не удалось отправить сообщение, повторите попытку позже");
                    }
                }
            });
        })

        $('#sendChatMessage').on('click', function () {
            let text = $('#textMessage').val();
            let ticketId = $('#currentChatCard').data('ticket-id');

            $.ajax({
                type: 'POST',
                url: '/ajax/chat/send-ticket-message',
                data: {ticketId: ticketId, pId: pId, text: text},
                success: function (data) {
                    if (data.result) {
                        $('#textMessage').val('');
                        getChat(ticketId);
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
            let chatId = $(this).data('ticketId');
            $('#textMessage').val('');

            getChat(chatId);
        });


        function getChat(chatId) {
            $.ajax({
                type: 'GET',
                url: '/ajax/chat/get-ticket',
                data: {id: chatId, pId: pId},
                success: function (data) {
                    if (!data.result) {
                        toastr.success(data.message);
                        $('#currentChatCard').hide(500);
                    } else {
                        console.log(data);
                        let text = data.ticket.text;
                        let chatId = data.ticket.id;
                        let closed = data.ticket.is_closed;

                        $('#currentChatCard').data('ticket-id', chatId);
                        $('#currentChatCard .ticket--text').text(text);
                        // $('#currentChatCard .chat--car-region').text(carRegion);

                        $('#currentChatCard .dialog-chat .messages').empty();

                        let messagesHtml = '';
                        $.each(data.messages, function (i, item) {
                            let messageHtml = '';
                            let textMessage = item.text;
                            let createdAt = item.created_at;
                            let isAdminMessage = false;
                            if (item.admin_id > 0) {
                                isAdminMessage = true;
                            }

                            if (isAdminMessage) {
                                messageHtml = '<div class="d-flex flex-column mb-5 align-items-start">' +
                                    '<div class="d-flex align-items-center"><div>' +
                                    '<a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">' +
                                    '<div class="d-flex font-size-h4 align-items-center font-weight-bold">' +
                                    '<div class="chat--car-number">Администратор</div></div>' +
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