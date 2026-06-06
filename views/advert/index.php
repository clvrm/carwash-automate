<?php

use app\models\ar\Advertising;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/**
 * @var Advertising $model
 * @var int $subscribersCount
 */

$this->title = 'Реклама';
$this->params['header_button'] = '<div class="d-block mr-8">
      <div>ПРОСМОТРЫ</div>
      <div class="text-primary font-weight-bold font-size-h4">' . ($model->views ?? 0) . '
      <i class="far fa-question-circle p-1" data-toggle="popover" title="Просмотры" data-html="true"
      data-content="Общее количество уникальных просмотров рекламной публикации. <br>Важно! Нет повторных показов рекламы для клиента."></i>
      </div>
    </div><div class="d-block mr-12">
      <div>ПОДПИСЧИКИ</div>
      <div class="text-primary font-weight-bold font-size-h4">' . $subscribersCount . '
      <i class="far fa-question-circle p-1" data-toggle="popover" title="Подписчики" data-html="true"
      data-content="Это клиенты, которые закреплены за Вашей автомойкой. <br>Важно!  Для того, чтобы стать подписчиком, при регистрации клиент ввел код Вашей автомойки или записался на автомойку более 3х раз подряд."></i>
      </div></div>';


?>

<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<div class="card card-custom card-stretch gutter-b font-size-h2-lg">

    <div class="card-body">
        <div class="row">
            <div class="col-md-5">
                <form action="/">
                    <h3>Создание рассылки</h3>

                    <div class="mt-4 form-group">
                        <label>Сайт</label>
                        <?= Html::activeInput('text', $model, 'site', ['id' => 'siteInput', 'class' => 'form-control',
                            'placeholder' => 'Ваш сайт', 'required' => false]) ?>
                    </div>

                    <div class="form-group">
                        <label>Номер телефона</label>
                        <?= Html::activeInput('text', $model, 'phone', ['id' => 'phoneInput', 'class' => 'form-control',
                            'placeholder' => 'Ваш телефон', 'required' => false]) ?>
                    </div>

                    <h3 class="mt-4 mb-4">Содержание рассылки</h3>
                    <div class="form-group">
                        <label>Заголовок <span class="required-field-star text-danger">*</span></label>
                        <?= Html::activeInput('text', $model, 'title', ['id' => 'titleInput', 'class' => 'form-control',
                            'placeholder' => 'Заголовок', 'required' => true]) ?>
                    </div>

                    <div class="form-group mb-1">
                        <label for="exampleTextarea">Основной текст <span
                                    class="required-field-star text-danger">*</span></label>
                        <?= Html::activeTextarea($model, 'text', ['id' => 'textInput', 'class' => 'form-control', 'rows' => 10,
                            'placeholder' => 'Текст рассылки', 'required' => true]) ?>
                    </div>
                    <?php Pjax::begin(['id' => 'banner']); ?>
                    <?php if ($model->banner) : ?>
                        <div class="d-flex align-items-center  justify-content-center mt-6 symbol symbol-120 symbol-2by3 flex-shrink-0">
                            <div id="uploadedBannerImage" class="symbol-label" data-image="<?= $model->banner ?? ' ' ?>"
                                 style="background-image: url('<?= $model->banner ?? ' ' ?>')"></div>
                        </div>
                        <div id="removeBanner" class="text-center text-danger cursor-pointer font-size-h5">
                            Удалить баннер
                        </div>
                    <?php endif; ?>
                    <div class="dropzone dropzone-default dz-clickable mt-4" id="advertFile">
                        <div class="dropzone-msg dz-message needsclick">
                            <h3 class="dropzone-msg-title">Перетащите файл сюда или нажмите для загрузки</h3>
                            <span class="dropzone-msg-desc">Макс. размер файла 1 Мб.</span>
                        </div>
                    </div>
                    <div class="mt-4 d-flex align-items-center text-dark">
                        <i class="fas fa-info-circle mr-4"></i>
                        <div class="text-muted font-size-h6">Можно прикрепить баннер, креатив, который будет
                            показываться Вашим
                            подписчикам
                        </div>

                    </div>
                    <?php Pjax::end(); ?>

                </form>
            </div>
            <div class="offset-md-1 col-md-6 mt-6 mt-md-0">
                <h3>Предпросмотр
                    <i class="far fa-question-circle p-1" data-toggle="popover" title="Предпросмотр" data-html="true"
                       data-content="Данная функция позволяет узнать, как будет выглядеть Ваша реклама для клиентов"></i>
                </h3>
                <div class="d-flex align-items-center mb-4 font-weight-bold">
                    <div class="text-muted mr-4">Статус:</div>
                    <div><?= $model::labelStatus($model->status) ?? '---' ?></div>
                </div>

                <div id="advertEditor"
                     style="padding: 20px 15px;height: auto;border: 1px solid #e4e6ef;border-radius: 5px;">
                    <h2 id="advertEditorTitle"><?= $model->title ?? 'Заголовок' ?></h2>
                    <div id="advertEditorText"><?= $model->text ?? 'Текст' ?></div>
                    <div id="advertEditorSite"><?= $model->site ?? 'www.carwash.ru' ?></div>
                    <div id="advertEditorPhone"><?= $model->phone ?? '+7 (999)-999-99-99' ?></div>
                    <div id="advertEditorImage" class="mt-8"
                         style="background-image: url(<?= $model->banner ?? '' ?>);
                                 background-size: contain;min-height: 260px;max-height: 500px;width: 100%;
                                 background-repeat: no-repeat;background-position: center;"></div>
                </div>
                <button type="submit" class="btn btn-lg btn-primary d-flex justify-content-center mt-4">Опубликовать
                    рассылку
                </button>
            </div>

        </div>

    </div>
</div>
<?php ActiveForm::end(); ?>


<script>
    $(document).ready(function () {
        let cwId = $('#mainInfoBlock').data('cw-id');

        checkRequiredFields();

        function checkRequiredFields() {
            if ($('#uploadedBannerImage').length > 0) {
                $('.required-field-star').hide();
                document.getElementById("titleInput").required = false;
                document.getElementById("textInput").required = false;
            } else {
                $('.required-field-star').show();
                document.getElementById("titleInput").required = true;
                document.getElementById("textInput").required = true;
            }
        }


        $(document).on('pjax:success', function (event) {
            var imageDropzone = new Dropzone("#advertFile", {url: "/ajax/advert/upload-banner?carwashId=" + cwId});
            imageDropzone.on("success", function (file, response) {
                $.pjax.reload({
                    url: '/advert/index',
                    container: "#banner",
                });
            });
            let bannerImage = $('#uploadedBannerImage').data('image');
            $('#advertEditorImage').css('background-image', 'url(' + bannerImage + ')');

            checkRequiredFields();
        });

        var imageDropzone = new Dropzone("#advertFile", {url: "/ajax/advert/upload-banner?carwashId=" + cwId});

        imageDropzone.on("success", function (file, response) {
            $.pjax.reload({
                url: '/advert/index',
                container: "#banner",
            });
        });

        $('#siteInput').on('input', function () {
            let val = $(this).val();
            $('#advertEditorSite').text(val);
        });


        $(document).on('click', '#removeBanner', function () {
            $.ajax({
                type: 'GET',
                url: '/ajax/advert/remove-banner',
                data: {carwashId: cwId},
                success: function (data) {
                    if (data.result) {
                        $('#uploadedBannerImage').remove();
                        $('#advertEditorImage').css('background-image', 'none');
                        $('#removeBanner').remove();
                        toastr.success('Баннер успешно удален');
                        checkRequiredFields();
                    } else {
                        toastr.danger('Ошибка при удалении баннера. Попробуйте позже или обратитесь к администрации');
                    }
                }
            });
        });


        $('#phoneInput').on('input', function () {
            let val = $(this).val();
            $('#advertEditorPhone').text(val);
        });
        $('#titleInput').on('input', function () {
            let val = $(this).val();
            $('#advertEditorTitle').text(val);
        });
        $('#textInput').on('input', function () {
            let val = $(this).val();
            $('#advertEditorText').text(val);
        });


    });
</script>