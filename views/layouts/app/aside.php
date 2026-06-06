<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
    <!--begin::Brand-->
    <div class="brand flex-column-auto" id="kt_brand">
        <!--begin::Logo-->
        <a href="/" class="brand-logo">
            <img alt="Logo" src="/media/logos/logo-new.png" style="max-height: 60px;"/>
        </a>
        <!--end::Logo-->
        <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
							<span class="svg-icon svg-icon svg-icon-xl">
								<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-left.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                     width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon points="0 0 24 0 24 24 0 24"/>
										<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z"
                                              fill="#000000" fill-rule="nonzero"
                                              transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)"/>
										<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z"
                                              fill="#000000" fill-rule="nonzero" opacity="0.3"
                                              transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)"/>
									</g>
								</svg>
                                <!--end::Svg Icon-->
							</span>
        </button>
        <!--end::Toolbar-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside Menu-->
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
        <div class="flex-column-auto d-md-none d-xs-show d-sm-show" id="kt_mobile_brand">
            <!--begin::Logo-->
            <a href="/" class="brand-logo">
                <img alt="Logo" src="/media/logos/logo.png"/>
            </a>
        </div>
        <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1"
             data-menu-dropdown-timeout="500">
            <ul class="menu-nav">
                <li class="menu-item <?= in_array($this->context->route, ['site/index']) ? 'menu-item-active' : '' ?>"
                    aria-haspopup="true">
                    <a href="/" class="menu-link">
										<span class="svg-icon menu-icon">
											<img src="/media/service/menu/journal.png">
										</span>
                        <span class="menu-text">Журнал</span>
                    </a>
                </li>
                <?php if (Yii::$app->user->can('perm_edit_pricelist') || Yii::$app->user->can('perm_edit_carwash_info')
                    || Yii::$app->user->can('perm_change_record_setting') || Yii::$app->user->can('perm_create_edit_sales')): ?>
                    <li class="menu-item menu-item-submenu <?= in_array($this->context->route, ['sales/index', 'service/wash', 'carwash/client', 'carwash/settings']) ? 'menu-item-open' : '' ?>"
                        aria-haspopup="true" data-menu-toggle="hover">
                        <a href="javascript:;" class="menu-link menu-toggle">
										<span class="svg-icon menu-icon">
																			<img src="/media/service/menu/carwash.png">
										</span>
                            <span class="menu-text">Автомойка</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="menu-submenu">
                            <i class="menu-arrow"></i>
                            <ul class="menu-subnav">
                                <li class="menu-item menu-item-parent" aria-haspopup="true">
												<span class="menu-link">
													<span class="menu-text">Автомойка</span>
												</span>
                                </li>

                                <?php if (Yii::$app->user->can('perm_edit_carwash_info')): ?>
                                    <li class="menu-item menu-item-submenu <?= in_array($this->context->route, ['carwash/client']) ? 'menu-item-active' : '' ?>"
                                        aria-haspopup="true" data-menu-toggle="hover">
                                        <a href="/carwash/client" class="menu-link menu-toggle">
                                            <i class="menu-bullet menu-bullet-dot">
                                                <span></span>
                                            </i>
                                            <span class="menu-text">Для клиента</span>
                                            <span class="menu-label"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (Yii::$app->user->can('perm_change_record_setting')): ?>
                                    <li class="menu-item menu-item-submenu <?= in_array($this->context->route, ['carwash/settings']) ? 'menu-item-active' : '' ?>"
                                        aria-haspopup="true" data-menu-toggle="hover">
                                        <a href="/carwash/settings" class="menu-link menu-toggle">
                                            <i class="menu-bullet menu-bullet-dot">
                                                <span></span>
                                            </i>
                                            <span class="menu-text">Настройки</span>
                                            <span class="menu-label"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (Yii::$app->user->can('perm_edit_pricelist')): ?>
                                    <li class="menu-item menu-item-submenu <?= in_array($this->context->route, ['service/wash']) ? 'menu-item-active' : '' ?>"
                                        aria-haspopup="true" data-menu-toggle="hover">
                                        <a href="/service/wash" class="menu-link menu-toggle">
                                            <i class="menu-bullet menu-bullet-dot">
                                                <span></span>
                                            </i>
                                            <span class="menu-text">Прайс-лист</span>
                                            <span class="menu-label"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (Yii::$app->user->can('perm_create_edit_sales')): ?>
                                    <li class="menu-item menu-item-submenu <?= in_array($this->context->route, ['sales/index']) ? 'menu-item-active' : '' ?>"
                                        aria-haspopup="true" data-menu-toggle="hover">
                                        <a href="/sales/" class="menu-link menu-toggle">
                                            <i class="menu-bullet menu-bullet-dot">
                                                <span></span>
                                            </i>
                                            <span class="menu-text">Скидки</span>
                                            <span class="menu-label"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (Yii::$app->user->can('perm_edit_personal')): ?>
                    <li class="menu-item <?= in_array($this->context->route, ['personal/index']) ? 'menu-item-active' : '' ?>"
                        aria-haspopup="true">
                        <a href="/personal/index" class="menu-link">
										<span class="svg-icon menu-icon">
                                            <img src="/media/service/menu/personal.png">
										</span>
                            <span class="menu-text">Персонал</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (Yii::$app->user->can('perm_create_mailing')): ?>
                    <li class="menu-item <?= in_array($this->context->route, ['advert/index']) ? 'menu-item-active' : '' ?>"
                        aria-haspopup="true">
                        <a href="/advert/index" class="menu-link">
										<span class="svg-icon menu-icon">
											<img src="/media/service/menu/adversting.png">
										</span>
                            <span class="menu-text">Реклама</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (Yii::$app->user->can('perm_view_analytics')): ?>
                    <li class="menu-item menu-item-submenu <?= in_array($this->context->route, ['analytics/orders', 'analytics/clients', 'analytics/finance']) ? 'menu-item-open' : '' ?>"
                        aria-haspopup="true" data-menu-toggle="hover">
                        <a href="javascript:;" class="menu-link menu-toggle">
										<span class="svg-icon menu-icon">
                                            <img src="/media/service/menu/analitics.png">
										</span>
                            <span class="menu-text">Аналитика</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="menu-submenu">
                            <i class="menu-arrow"></i>
                            <ul class="menu-subnav">
                                <li class="menu-item menu-item-parent" aria-haspopup="true">
												<span class="menu-link">
													<span class="menu-text">Аналитика</span>
												</span>
                                </li>
                                <li class="menu-item menu-item-submenu <?= in_array($this->context->route, ['analytics/orders']) ? 'menu-item-active' : '' ?>"
                                    aria-haspopup="true" data-menu-toggle="hover">
                                    <a href="/analytics/orders" class="menu-link menu-toggle">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">Заказы</span>
                                        <span class="menu-label"></span>
                                    </a>
                                </li>
                                <li class="menu-item menu-item-submenu <?= in_array($this->context->route, ['analytics/clients']) ? 'menu-item-active' : '' ?>"
                                    aria-haspopup="true" data-menu-toggle="hover">
                                    <a href="/analytics/clients" class="menu-link menu-toggle">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">Клиенты</span>
                                        <span class="menu-label"></span>
                                    </a>
                                </li>
                                <li class="menu-item menu-item-submenu <?= in_array($this->context->route, ['analytics/finance']) ? 'menu-item-active' : '' ?>"
                                    aria-haspopup="true" data-menu-toggle="hover">
                                    <a href="/analytics/finance" class="menu-link menu-toggle">
                                        <i class="menu-bullet menu-bullet-dot">
                                            <span></span>
                                        </i>
                                        <span class="menu-text">Финансы</span>
                                        <span class="menu-label"></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <li class="menu-item <?= in_array($this->context->route, ['profile/my']) ? 'menu-item-active' : '' ?>"
                    aria-haspopup="true">
                    <a href="/profile/my" class="menu-link">
										<span class="svg-icon menu-icon">
                                            <img src="/media/service/menu/person.png">
										</span>
                        <span class="menu-text">Профиль</span>
                    </a>
                </li>
                <?php if (Yii::$app->user->can('perm_respond_reviews')): ?>
                    <li class="menu-item <?= in_array($this->context->route, ['chat/index']) ? 'menu-item-active' : '' ?>"
                        aria-haspopup="true">
                        <a href="/chat/index" class="menu-link">
										<span class="svg-icon menu-icon">
                                            <img src="/media/service/menu/forum.png">
										</span>
                            <span class="menu-text">Чат</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="menu-item <?= in_array($this->context->route, ['chat/support']) ? 'menu-item-active' : '' ?>"
                        aria-haspopup="true">
                        <a href="/chat/support" class="menu-link">
										<span class="svg-icon menu-icon">
                                            <img src="/media/service/menu/forum.png">
										</span>
                            <span class="menu-text">Поддержка</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li style="display:none" class="menu-item menu-item-submenu <?= in_array($this->context->route, ['partner-shops/equipment', 'documentation/index']) ? 'menu-item-open' : '' ?>"
                    aria-haspopup="true" data-menu-toggle="hover">
                    <a href="javascript:;" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <img src="/media/service/menu/others.png">
                        </span>
                        <span class="menu-text">Прочее</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu">
                        <i class="menu-arrow"></i>
                        <ul class="menu-subnav">
                            <li class="menu-item menu-item-parent" aria-haspopup="true">
												<span class="menu-link">
													<span class="menu-text">Прочее</span>
												</span>
                            </li>
                            <li class="menu-item menu-item-submenu <?= in_array($this->context->route, ['partner-shops/equipment']) ? 'menu-item-active' : '' ?>"
                                aria-haspopup="true" data-menu-toggle="hover">
                                <a href="/partner-shops/equipment" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-dot">
                                        <span></span>
                                    </i>
                                    <span class="menu-text">Склад</span>
                                    <span class="menu-label"></span>
                                </a>
                            </li>
<!--                            <li class="menu-item menu-item-submenu --><?php //= in_array($this->context->route, ['documentation/index']) ? 'menu-item-active' : '' ?><!--"-->
<!--                                aria-haspopup="true" data-menu-toggle="hover">-->
<!--                                <a href="/documentation?category=1" class="menu-link menu-toggle">-->
<!--                                    <i class="menu-bullet menu-bullet-dot">-->
<!--                                        <span></span>-->
<!--                                    </i>-->
<!--                                    <span class="menu-text">Документация</span>-->
<!--                                    <span class="menu-label"></span>-->
<!--                                </a>-->
<!--                            </li>-->
                        </ul>
                    </div>
                </li>


                <li class="menu-item mt-10" aria-haspopup="true">
                    <a href="/auth/logout" class="menu-link">
										<span class="svg-icon menu-icon">
                                            <img src="/media/service/menu/logout.png">
										</span>
                        <span class="menu-text">Выйти</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>