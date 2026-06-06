
<?php
$this->title = 'Тестовая таблица';

$postCount = 2;
$workStartHour = 9;
$workEndHour = 19;

?>

<div class="table-wrapper">
    <div class="table-time-grid">
        <div class="timetable-axis">
            <?php for ($i = $workStartHour; $i <= $workEndHour; $i++) : ?>
                <div class="axis-item"><?= $i ?>:00</div>
            <?php endfor; ?>
        </div>
        <div class="timetable-columns-post">
            <?php for ($post = 1; $post <= $postCount; $post++): ?>
                <?php if ($post == 1): ?>
                    <div class="timetable-column pred-column">
                        <div class="timetable-column-header font-size-sm"></div>
                        <div class="timetable-column-grid">
                            <?php for ($i = $workStartHour; $i < $workEndHour; $i++) : ?>
                                <div class="grid-item first-column"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="timetable-column">
                    <div class="timetable-column-header font-size-sm">Пост <?= $post ?></div>
                    <div class="timetable-column-grid">
                        <?php for ($i = $workStartHour; $i < $workEndHour; $i++) : ?>
                            <div class="grid-item first-column"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endfor; ?>
            <div id="tableJs"></div>
        </div>
    </div>


</div>
<!--<script type="text/babel" src="/js/react-grid-layout.js"></script>-->
<style>
    .react-resizable {
        position: relative;
    }
    .react-resizable-handle.react-draggable {
        position: absolute;
        width: 20px;
        height: 20px;
        bottom: 0;
        right: 0;
        background: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pg08IS0tIEdlbmVyYXRvcjogQWRvYmUgRmlyZXdvcmtzIENTNiwgRXhwb3J0IFNWRyBFeHRlbnNpb24gYnkgQWFyb24gQmVhbGwgKGh0dHA6Ly9maXJld29ya3MuYWJlYWxsLmNvbSkgLiBWZXJzaW9uOiAwLjYuMSAgLS0+DTwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DTxzdmcgaWQ9IlVudGl0bGVkLVBhZ2UlMjAxIiB2aWV3Qm94PSIwIDAgNiA2IiBzdHlsZT0iYmFja2dyb3VuZC1jb2xvcjojZmZmZmZmMDAiIHZlcnNpb249IjEuMSINCXhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiDQl4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjZweCIgaGVpZ2h0PSI2cHgiDT4NCTxnIG9wYWNpdHk9IjAuMzAyIj4NCQk8cGF0aCBkPSJNIDYgNiBMIDAgNiBMIDAgNC4yIEwgNCA0LjIgTCA0LjIgNC4yIEwgNC4yIDAgTCA2IDAgTCA2IDYgTCA2IDYgWiIgZmlsbD0iIzAwMDAwMCIvPg0JPC9nPg08L3N2Zz4=');
        background-position: bottom right;
        padding: 0 3px 3px 0;
        background-repeat: no-repeat;
        background-origin: content-box;
        box-sizing: border-box;
        cursor: se-resize;
        /* Since this handle is absolutely positioned, we don't want the
           draggable transforms to actually move it */
        transform: none !important;
        -webkit-transform: none !important;
        -ms-transform: none !important;
        -o-transform: none !important;
        -moz-transform: none !important;
    }

    .react-grid-layout {
        position: relative;
        transition: height 200ms ease;
    }

    .react-grid-item {
        /*background: #d6d6d6;*/
        transition: all 200ms ease;
        transition-property: left, top;
    }

    .react-grid-item.cssTransforms {
        transition-property: transform;
    }

    .react-grid-item.resizing {
        z-index: 1;
        will-change: width, height;
    }

    .react-grid-item.react-draggable-dragging {
        transition: none;
        z-index: 3;
        will-change: transform;
    }

    .react-grid-item.react-grid-placeholder {
        background: red;
        opacity: 0.2;
        transition-duration: 100ms;
        z-index: 2;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        -o-user-select: none;
        user-select: none;
    }

    .react-grid-item > .react-resizable-handle {
        position: absolute;
        float: left;
        width: 20px;
        height: 20px;
        margin-top: -20px;
        right: 3px;
        background: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pg08IS0tIEdlbmVyYXRvcjogQWRvYmUgRmlyZXdvcmtzIENTNiwgRXhwb3J0IFNWRyBFeHRlbnNpb24gYnkgQWFyb24gQmVhbGwgKGh0dHA6Ly9maXJld29ya3MuYWJlYWxsLmNvbSkgLiBWZXJzaW9uOiAwLjYuMSAgLS0+DTwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DTxzdmcgaWQ9IlVudGl0bGVkLVBhZ2UlMjAxIiB2aWV3Qm94PSIwIDAgNiA2IiBzdHlsZT0iYmFja2dyb3VuZC1jb2xvcjojZmZmZmZmMDAiIHZlcnNpb249IjEuMSINCXhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiDQl4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjZweCIgaGVpZ2h0PSI2cHgiDT4NCTxnIG9wYWNpdHk9IjAuMzAyIj4NCQk8cGF0aCBkPSJNIDYgNiBMIDAgNiBMIDAgNC4yIEwgNCA0LjIgTCA0LjIgNC4yIEwgNC4yIDAgTCA2IDAgTCA2IDYgTCA2IDYgWiIgZmlsbD0iIzAwMDAwMCIvPg0JPC9nPg08L3N2Zz4=");
        background-position: bottom right;
        padding: 0 3px 3px 0;
        background-repeat: no-repeat;
        background-origin: content-box;
        box-sizing: border-box;
        cursor: se-resize;
    }

</style>
<style>
    .timetable-popup {
        display: none;
    }

    .timetable-item {
        height: 55px;
        position: relative;
    }


    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-axis] {
        width: 60px;
    }

    [class~=tiva-timetable] [class~=timetable-week][class~=show-time] [class~=timetable-column] [class~=timetable-column-header] {
        height: 37.5pt;
    }

    .tiva-timetable .loading {
        text-align: center;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-columns] [class~=timetable-column], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-axis] {
        float: left;
    }

    .tiva-timetable .timetable-week.show-time .timetable-axis {
        padding-top: 3.75pc;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header, .tiva-timetable .loading {
        padding-left: 0;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th {
        border-left-width: .0625pc;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-axis] [class~=axis-item] {
        height: 55px;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th {
        border-bottom-width: .0625pc;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-axis] {
        padding-top: 2.25pc;
    }

    .tiva-timetable .loading {
        padding-bottom: 1.041666667in;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-header][class~=last-column] {
        border-right-width: medium;
    }

    .tiva-timetable {
        font-family: "Lato", sans-serif;
    }

    .tiva-timetable .loading, .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        padding-right: 0;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-columns] {
        margin-left: 3.75pc;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-header] {
        height: .354166667in;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-axis] [class~=axis-item] {
        font-size: 15px;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-header][class~=last-column] {
        border-right-style: none;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-axis] [class~=axis-item] {
        line-height: .9375pc;
    }

    .tiva-timetable {
        font-size: 12pt;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-header][class~=last-column] {
        border-right-color: currentColor;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-header][class~=last-column] {
        border-image: none;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title], [class~=tiva-timetable] table[class~=timetable-month] {
        width: 100%;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-header] {
        line-height: .333333333in;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td[class~=calendar-day][class~=today] {
        padding-left: 7.5pt;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-columns] [class~=timetable-column] {
        width: 14.2857%;
        min-width: 150px;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-columns] [class~=timetable-column] {
        position: relative;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th[class~=timetable-header], .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        height: .270833333in;
    }

    [class~=tiva-timetable] [class~=timetable-week][class~=show-time] [class~=timetable-column] [class~=timetable-column-content] {
        top: 52.5pt;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th, .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        border-right-width: .0625pc;
    }

    .tiva-timetable .loading {
        padding-top: 1.041666667in;
    }

    .tiva-timetable {
        color: #535353;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-header], .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        background-color: #c594c5;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header, [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-header], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title], [class~=tiva-timetable] table[class~=timetable-month] {
        color: #fff;
    }

    [class~=time-navigation] [class~=navi-time] {
        display: inline-block;
    }

    .tiva-timetable {
        line-height: 1.5pc;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title] {
        display: block;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title] {
        position: absolute;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title] {
        top: 0;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        padding-bottom: 7.5pt;
    }

    [class~=time-navigation] [class~=navi-prev] {
        margin-right: .208333333in;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title] {
        height: .5625in;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th {
        border-top-width: .0625pc;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th {
        border-left-style: solid;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th {
        border-bottom-style: solid;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title] {
        text-decoration: none;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-header], .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        font-weight: 400;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td[class~=calendar-day][class~=today] {
        padding-bottom: 5px;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        padding-top: 7.5pt;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header, [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
        text-align: center;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header, [class~=tiva-timetable] table[class~=timetable-month] th {
        border-right-style: solid;
    }

    .tiva-timetable .timetable-list .timetable-header span {
        float: right;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title] {
        outline: none;
    }

    [class~=time-navigation] [class~=navi-next] {
        margin-left: 20px;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th {
        border-top-style: solid;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title] {
        padding-left: 0;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-header] {
        text-align: left;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title]:hover {
        opacity: .8;
    }

    .time-navigation .navi-icon {
        font-family: "Zapf Dingbats";
    }

    [class~=tiva-timetable] table[class~=timetable-month] {
        box-sizing: border-box;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-header], .tiva-timetable .timetable-list .timetable-content {
        padding-left: 11.25pt;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title], [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-header] {
        padding-bottom: 0;
    }

    .tiva-timetable {
        -webkit-font-smoothing: antialiased;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td[class~=calendar-day][class~=today] {
        padding-right: 6px;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-header], .tiva-timetable .timetable-list .timetable-content {
        padding-right: 11.25pt;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title], [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-header] {
        padding-top: 0;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title] {
        padding-right: 0;
    }

    .time-navigation .navi-icon {
        font-size: 18px;
    }

    .time-navigation .navi-icon {
        cursor: pointer;
    }

    .tiva-timetable {
        font-smoothing: antialiased;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title] {
        -webkit-box-shadow: 0 0 1.375pc 0 rgba(0, 0, 0, .1);
    }

    .time-navigation .navi-icon {
        user-select: none;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title] {
        box-shadow: 0 0 16.5pt 0 rgba(0, 0, 0, .1);
    }

    .time-navigation .navi-icon {
        -webkit-user-select: none;
    }

    .tiva-timetable .timetable-list .timetable-content {
        padding-bottom: 11.25pt;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header, [class~=tiva-timetable] table[class~=timetable-month] th {
        border-right-color: #d1d1d1;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        border-image: none;
    }

    .time-navigation .navi-icon {
        -moz-user-select: none;
    }

    .tiva-timetable .timetable-list .timetable-content {
        padding-top: 11.25pt;
    }

    [class~=tiva-timetable] table[class~=timetable-month], .tiva-timetable .timetable-list .timetable-content {
        border-left-width: 1px;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        -webkit-box-sizing: content-box;
    }

    .tiva-timetable .timetable-list .timetable-content {
        border-left-style: solid;
    }

    [class~=tiva-timetable] table[class~=timetable-month] {
        border-collapse: collapse;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
        position: absolute;
    }

    .tiva-timetable {
        -moz-osx-font-smoothing: grayscale;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        -moz-box-sizing: content-box;
    }

    [class~=tiva-timetable] table[class~=timetable-month] {
        border-spacing: 0;
    }

    .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
        box-sizing: content-box;
    }

    .time-navigation .navi-icon {
        -ms-user-select: none;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap], [class~=tiva-timetable] [class~=timetable-week], [class~=tiva-timetable] [class~=timetable-list] {
        width: 100%;
    }

    [class~=tiva-timetable] table[class~=timetable-month] {
        table-layout: fixed;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th, .tiva-timetable .timetable-list .timetable-content {
        border-left-color: #d1d1d1;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
        top: 50%;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
        left: 50%;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] {
        top: 46px;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-day]:last-child, [class~=tiva-timetable] table[class~=timetable-month] {
        border-bottom-width: .0625pc;
    }

    .tiva-timetable {
        margin-left: 0;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
        -webkit-transform: translate(-50%, -50%);
    }

    .tiva-timetable .timetable-list .timetable-header span {
        font-size: .15625in;
    }

    [class~=tiva-timetable] [class~=timetable-list], [class~=tiva-timetable] [class~=timetable-week], .tiva-timetable {
        float: left;
    }

    .tiva-timetable .timetable-list .timetable-content {
        border-image: none;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-day]:last-child, [class~=tiva-timetable] table[class~=timetable-month] th {
        border-bottom-color: #d1d1d1;
    }

    .tiva-timetable .timetable-list .timetable-content, [class~=tiva-timetable] table[class~=timetable-month] {
        border-right-width: .010416667in;
    }

    [class~=timetable-popup] [class~=popup-body] [class~=timetable-image] img {
        max-width: 100%;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
        -ms-transform: translate(-50%, -50%);
    }

    [class~=tiva-timetable] table[class~=timetable-month], .tiva-timetable .timetable-list .timetable-content {
        border-right-style: solid;
    }

    .tiva-timetable {
        margin-bottom: .208333333in;
    }

    [class~=tiva-timetable] table[class~=timetable-month], [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-day]:last-child {
        border-bottom-style: solid;
    }

    .tiva-timetable .timetable-list .timetable-content {
        border-right-color: #d1d1d1;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
        -o-transform: translate(-50%, -50%);
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
        transform: translate(-50%, -50%);
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
        max-width: 90%;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
        overflow: hidden;
    }

    .tiva-timetable {
        margin-right: 0;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item], [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] {
        text-align: left;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-name] {
        font-size: 14px;
    }

    .tiva-timetable {
        margin-top: .208333333in;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-name] {
        line-height: 14px;
    }

    [class~=tiva-timetable] table[class~=timetable-month] {
        border-top-width: .0625pc;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-name] {
        white-space: nowrap;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-day]:last-child {
        border-image: none;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        position: absolute;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        top: .5625pc;
    }

    [class~=tiva-timetable] table[class~=timetable-month] {
        border-left-style: solid;
    }

    [class~=timetable-popup] [class~=popup-body] [class~=timetable-image] img {
        margin-bottom: .15625in;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-name] {
        margin-bottom: 6pt;
    }

    [class~=mfp-bg][class~=my-mfp-zoom-in] {
        background: rgba(66, 65, 65, .73);
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-time] {
        font-size: 9.75pt;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        left: 0;
    }

    [class~=timetable-popup] [class~=mfp-close] {
        top: .083333333in;
    }

    .timetable-popup .popup-header {
        padding-left: 15pt;
    }

    .timetable-popup .popup-header {
        padding-bottom: 15pt;
    }

    .tiva-timetable {
        width: 100%;
    }

    [class~=tiva-timetable] table[class~=timetable-month] {
        border-top-style: solid;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-time] {
        line-height: .135416667in;
    }

    .timetable-popup, [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item], [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] {
        position: relative;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] {
        padding-left: .15625in;
    }

    [class~=tiva-timetable] table[class~=timetable-month] {
        border-left-color: #d1d1d1;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        width: 5.25pt;
    }

    [class~=time-navigation], .timetable-popup .popup-header {
        text-align: center;
    }

    .timetable-popup .popup-header {
        padding-right: 15pt;
    }

    .timetable-popup .popup-header {
        padding-top: 15pt;
    }

    [class~=time-navigation], .timetable-popup .popup-header {
        font-size: .1875in;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        height: .4375pc;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th {
        border-top-color: #d1d1d1;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=calendar-daycounter] {
        margin-bottom: 10px;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        border-radius: 15px;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        -moz-border-radius: .9375pc;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th {
        border-image: none;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td[class~=calendar-day][class~=today] {
        padding-top: .3125pc;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        -webkit-border-radius: .15625in;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        -webkit-transition: all .3s ease 0s;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th[class~=timetable-header] {
        padding-left: 0;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        -o-transition: all .3s ease 0s;
    }

    [class~=time-navigation] {
        text-transform: uppercase;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color] {
        transition: all .3s ease 0s;
    }

    .tiva-timetable .timetable-list .timetable-item .timetable-title {
        font-size: 15px;
    }

    .tiva-timetable .timetable-list .timetable-item .timetable-title {
        line-height: .208333333in;
    }

    .timetable-popup .popup-header {
        color: #fff;
    }

    [class~=time-navigation] {
        margin-bottom: 22.5pt;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item][class~=first-column], [class~=tiva-timetable] table[class~=timetable-month] td {
        border-left-width: .0625pc;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td {
        border-bottom-width: .0625pc;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th[class~=timetable-header] {
        padding-bottom: .104166667in;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-time] {
        white-space: nowrap;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-time] {
        margin-bottom: 1px;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item], [class~=tiva-timetable] table[class~=timetable-month] td {
        border-right-width: .75pt;
    }

    .tiva-timetable .timetable-list .timetable-item .timetable-title {
        text-decoration: none;
    }

    .timetable-popup .popup-header {
        font-weight: 400;
    }

    .tiva-timetable .timetable-list .timetable-item .timetable-title {
        outline: none;
    }

    .timetable-popup .popup-header {
        border-top-left-radius: .03125in;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td, [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item] {
        border-right-style: solid;
    }

    .timetable-popup {
        background: #fff;
    }

    .timetable-popup {
        width: auto;
    }

    .timetable-popup .popup-header {
        border-top-right-radius: 3px;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] {
        margin-bottom: 5px;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item] {
        border-right-color: #e8e8e8;
    }

    [class~=timetable-popup] [class~=popup-body] {
        padding-left: 22.5pt;
    }

    .tiva-timetable .timetable-list .timetable-item .timetable-time {
        font-weight: bold;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td {
        border-top-width: .0625pc;
    }

    [class~=tiva-timetable] table[class~=timetable-month], [class~=tiva-timetable] table[class~=timetable-month] td {
        border-bottom-color: #d1d1d1;
    }

    .timetable-popup {
        max-width: 5.208333333in;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] {
        padding-left: .135416667in;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td {
        border-left-style: solid;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td {
        border-bottom-style: solid;
    }

    [class~=timetable-popup] [class~=popup-body] {
        padding-bottom: 22.5pt;
    }

    .timetable-popup {
        margin-left: auto;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color][class~=color-1], [class~=timetable-popup] [class~=popup-header][class~=color-1], .tiva-timetable .timetable-list .timetable-item .timetable-color.color-1, [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-title][class~=color-1] {
        background: #5fb3b3;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item] {
        border-image: none;
    }

    .timetable-popup .popup-header.color-2 {
        background: #69c;
    }

    [class~=timetable-popup] [class~=popup-header][class~=color-3] {
        background: #f5d76a;
    }

    [class~=timetable-popup] [class~=popup-body] {
        padding-right: 22.5pt;
    }

    [class~=timetable-popup] [class~=popup-body] {
        padding-top: 22.5pt;
    }

    [class~=timetable-popup] [class~=popup-header][class~=color-3] {
        color: #666;
    }

    [class~=timetable-popup] [class~=popup-body], [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-name], .tiva-timetable .timetable-list .timetable-item .timetable-time {
        color: #535353;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item] {
        border-bottom-width: 1px;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td, [class~=tiva-timetable] table[class~=timetable-month] {
        border-right-color: #d1d1d1;
    }

    [class~=timetable-popup] [class~=popup-body] {
        text-align: center;
    }

    .tiva-timetable .timetable-list .timetable-item .timetable-time {
        margin-right: .625pc;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item] {
        border-bottom-style: solid;
    }

    [class~=timetable-popup] [class~=popup-header][class~=color-4] {
        background: #e78b6c;
    }

    .timetable-popup {
        margin-bottom: 15pt;
    }

    [class~=tiva-timetable] table[class~=timetable-month], [class~=tiva-timetable] table[class~=timetable-month] td {
        border-top-color: #d1d1d1;
    }

    [class~=my-mfp-zoom-in][class~=mfp-bg], [class~=my-mfp-zoom-in] [class~=zoom-anim-dialog] {
        opacity: 0;
    }

    .timetable-popup {
        margin-right: auto;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item] {
        border-bottom-color: #e8e8e8;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item] {
        height: 54px;
    }

    .tiva-timetable .timetable-month .today .calendar-daycounter {
        background: #c594c5;
    }

    [class~=my-mfp-zoom-in] [class~=zoom-anim-dialog] {
        -webkit-transition: all .2s ease-in-out;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item] {
        -webkit-box-sizing: content-box;
    }

    [class~=my-mfp-zoom-in] [class~=zoom-anim-dialog] {
        -moz-transition: all .2s ease-in-out;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td {
        border-top-style: solid;
    }

    [class~=my-mfp-zoom-in] [class~=zoom-anim-dialog] {
        -o-transition: all .2s ease-in-out;
    }

    [class~=my-mfp-zoom-in] [class~=zoom-anim-dialog] {
        transition: all .2s ease-in-out;
    }

    [class~=tiva-timetable] table[class~=timetable-month] {
        border-image: none;
    }

    [class~=my-mfp-zoom-in] [class~=zoom-anim-dialog] {
        -webkit-transform: scale(.8);
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item] {
        -moz-box-sizing: content-box;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td {
        border-left-color: #d1d1d1;
    }

    [class~=timetable-popup] [class~=popup-body] [class~=timetable-desc] {
        text-align: justify;
    }

    .tiva-timetable .timetable-month .today .calendar-daycounter {
        padding-left: .052083333in;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item] {
        box-sizing: content-box;
    }

    .timetable-popup {
        margin-top: 15pt;
    }

    .tiva-timetable .timetable-month .today .calendar-daycounter {
        padding-bottom: 0;
    }

    [class~=my-mfp-zoom-in] [class~=zoom-anim-dialog] {
        -moz-transform: scale(.8);
    }

    [class~=tiva-timetable] table[class~=timetable-month] th[class~=timetable-header] {
        padding-right: 0;
    }

    .tiva-timetable .timetable-month .today .calendar-daycounter {
        padding-right: .052083333in;
    }

    [class~=my-mfp-zoom-in] [class~=zoom-anim-dialog] {
        -ms-transform: scale(.8);
    }

    [class~=tiva-timetable] table[class~=timetable-month] th[class~=timetable-header] {
        padding-top: .104166667in;
    }

    [class~=timetable-popup] [class~=popup-body] [class~=timetable-desc] {
        font-size: 14px;
    }

    .tiva-timetable .timetable-month .today .calendar-daycounter, .timetable-popup {
        border-radius: 2.25pt;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color][class~=color-2], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-title][class~=color-2], [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color][class~=color-2] {
        background: #69c;
    }

    [class~=tiva-timetable] table[class~=timetable-month] td {
        border-image: none;
    }

    [class~=my-mfp-zoom-in] [class~=zoom-anim-dialog] {
        -o-transform: scale(.8);
    }

    .tiva-timetable .timetable-month .today .calendar-daycounter {
        padding-top: 0;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color][class~=color-3], [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color][class~=color-3], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-title][class~=color-3] {
        background: #f5d76a;
    }

    [class~=timetable-popup] [class~=popup-body] [class~=timetable-time] {
        margin-bottom: .15625in;
    }

    [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color][class~=color-3], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-title][class~=color-3], [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color][class~=color-3] {
        color: #666;
    }

    [class~=my-mfp-zoom-in] [class~=zoom-anim-dialog] {
        transform: scale(.8);
    }

    [class~=timetable-popup] [class~=popup-body] [class~=timetable-time] {
        font-size: 12pt;
    }

    [class~=timetable-popup] [class~=popup-body] [class~=timetable-time] {
        color: #f15e6e;
    }

    [class~=my-mfp-zoom-in][class~=mfp-ready] [class~=zoom-anim-dialog] {
        opacity: 1;
    }

    [class~=my-mfp-zoom-in][class~=mfp-ready] [class~=zoom-anim-dialog] {
        -webkit-transform: scale(1);
    }

    [class~=my-mfp-zoom-in][class~=mfp-bg] {
        -webkit-transition: opacity .3s ease-out;
    }

    [class~=my-mfp-zoom-in][class~=mfp-bg] {
        -moz-transition: opacity .3s ease-out;
    }

    .tiva-timetable .timetable-month .today .calendar-daycounter {
        color: #fff;
    }

    .timetable-popup, .tiva-timetable .timetable-month .today .calendar-daycounter {
        -moz-border-radius: 2.25pt;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th[class~=timetable-header] {
        background-color: #c594c5;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color][class~=color-4], [class~=tiva-timetable] [class~=timetable-list] [class~=timetable-item] [class~=timetable-color][class~=color-4], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-title][class~=color-4] {
        background: #e78b6c;
    }

    [class~=my-mfp-zoom-in][class~=mfp-bg] {
        -o-transition: opacity .3s ease-out;
    }

    [class~=my-mfp-zoom-in][class~=mfp-ready] [class~=zoom-anim-dialog] {
        -moz-transform: scale(1);
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item][class~=first-column] {
        border-left-style: solid;
    }

    .tiva-timetable .timetable-month .today .calendar-daycounter, .timetable-popup {
        -webkit-border-radius: .03125in;
    }

    [class~=my-mfp-zoom-in][class~=mfp-ready] [class~=zoom-anim-dialog] {
        -ms-transform: scale(1);
    }

    [class~=my-mfp-zoom-in][class~=mfp-bg] {
        transition: opacity .3s ease-out;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th[class~=timetable-header] {
        font-weight: 400;
    }

    [class~=tiva-timetable] table[class~=timetable-month] th[class~=timetable-header] {
        text-align: center;
    }

    [class~=my-mfp-zoom-in][class~=mfp-ready] [class~=zoom-anim-dialog] {
        -o-transform: scale(1);
    }

    .tiva-timetable .timetable-month .today .calendar-daycounter {
        display: inline-block;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        position: absolute;
    }

    [class~=my-mfp-zoom-in][class~=mfp-ready] [class~=zoom-anim-dialog] {
        transform: scale(1);
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        top: 5.25pt;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        left: 0;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        width: 5.25pt;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        height: .4375pc;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        border-radius: .9375pc;
    }

    [class~=my-mfp-zoom-in][class~=mfp-removing] [class~=zoom-anim-dialog] {
        -webkit-transform: scale(.8);
    }

    [class~=my-mfp-zoom-in][class~=mfp-removing] [class~=zoom-anim-dialog] {
        -moz-transform: scale(.8);
    }

    [class~=my-mfp-zoom-in][class~=mfp-removing] [class~=zoom-anim-dialog] {
        -ms-transform: scale(.8);
    }

    [class~=my-mfp-zoom-in][class~=mfp-removing] [class~=zoom-anim-dialog] {
        -o-transform: scale(.8);
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        -moz-border-radius: .15625in;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        -webkit-border-radius: .15625in;
    }

    [class~=my-mfp-zoom-in][class~=mfp-removing] [class~=zoom-anim-dialog] {
        transform: scale(.8);
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        -webkit-transition: all .3s ease 0s;
    }

    [class~=my-mfp-zoom-in][class~=mfp-removing] [class~=zoom-anim-dialog] {
        opacity: 0;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        -o-transition: all .3s ease 0s;
    }

    .timetable-popup {
        box-shadow: 0 0 1.375pc 0 rgba(0, 0, 0, .2);
    }

    .tiva-timetable table.timetable-month td.calendar-day {
        color: #ababab;
    }

    .my-mfp-zoom-in.mfp-ready.mfp-bg {
        opacity: .5;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-color] {
        transition: all .3s ease 0s;
    }

    [class~=my-mfp-zoom-in][class~=mfp-removing][class~=mfp-bg] {
        opacity: 0;
    }

    .timetable-popup {
        -webkit-box-shadow: 0 0 1.375pc 0 rgba(0, 0, 0, .2);
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-title] {
        font-size: 9.75pt;
    }

    .timetable-popup {
        font-family: "Lato", sans-serif;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-title], .tiva-timetable table.timetable-month td.calendar-day {
        line-height: 20px;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-title] {
        text-decoration: none;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-title] {
        outline: none;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-header] span, .tiva-timetable table.timetable-month td.calendar-day {
        font-size: .145833333in;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-title] {
        display: block;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-time] {
        font-weight: bold;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-time] {
        margin-right: 3.75pt;
    }

    [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-time], [class~=tiva-timetable] [class~=timetable-month] [class~=timetable-item] [class~=timetable-name] {
        color: #535353;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item][class~=first-column] {
        border-left-color: #e8e8e8;
    }

    .tiva-timetable table.timetable-month td.calendar-day {
        text-align: right;
    }

    .tiva-timetable table.timetable-month td.calendar-day {
        vertical-align: top;
    }

    .tiva-timetable table.timetable-month td.calendar-day {
        padding-left: .104166667in;
    }

    .tiva-timetable table.timetable-month td.calendar-day {
        padding-bottom: .3125pc;
    }

    [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-grid] [class~=grid-item][class~=first-column] {
        border-image: none;
    }

    .tiva-timetable table.timetable-month td.calendar-day {
        padding-right: .104166667in;
    }

    .tiva-timetable table.timetable-month td.calendar-day {
        padding-top: .3125pc;
    }

    .tiva-timetable table.timetable-month td.calendar-day {
        height: 3.75pc;
    }

    @media (max-width: 768px) {
        .tiva-timetable .timetable-week.show-time .timetable-column .timetable-column-header {
            height: 26px;
        }

        [class~=tiva-timetable] table[class~=timetable-month] [class~=timetable-item] [class~=timetable-name], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-header] span {
            display: none;
        }

        [class~=tiva-timetable] [class~=timetable-week][class~=show-time] [class~=timetable-column] [class~=timetable-column-content] {
            top: 2.875pc;
        }

        [class~=tiva-timetable] [class~=timetable-week][class~=show-time] [class~=timetable-axis] {
            padding-top: .375in;
        }

        [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
            max-width: 80%;
        }

        [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-name] {
            font-size: 9.75pt;
        }

        [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-time] {
            font-size: .125in;
        }

        [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-axis] {
            width: 37.5pt;
        }

        [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-columns] {
            margin-left: .520833333in;
        }
    }

    @media (max-width: 420px) {
        .tiva-timetable .timetable-month .timetable-item .timetable-color {
            top: .5625pc;
        }

        .tiva-timetable .timetable-month .timetable-item .timetable-color {
            left: 0;
        }

        [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-title-wrap] {
            max-width: 70%;
        }

        .tiva-timetable table.timetable-month .timetable-item .timetable-time {
            font-size: 6.75pt;
        }

        .tiva-timetable .timetable-month .timetable-item .timetable-color {
            width: 5px;
        }

        .tiva-timetable .timetable-month .timetable-item .timetable-color {
            height: .3125pc;
        }

        [class~=time-navigation] {
            margin-bottom: 11.25pt;
        }

        [class~=tiva-timetable] table[class~=timetable-month] th[class~=timetable-header] {
            font-size: .875pc;
        }

        .tiva-timetable table.timetable-month td.calendar-day.today, [class~=tiva-timetable] table[class~=timetable-month] td[class~=calendar-day] {
            padding-left: .03125in;
        }

        .tiva-timetable table.timetable-month .timetable-item {
            padding-left: 5.25pt;
        }

        [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-name] {
            font-size: .114583333in;
        }

        .tiva-timetable table.timetable-month td.calendar-day.today, [class~=tiva-timetable] table[class~=timetable-month] td[class~=calendar-day] {
            padding-bottom: 0;
        }

        [class~=tiva-timetable] table[class~=timetable-month] td[class~=calendar-day], .tiva-timetable table.timetable-month td.calendar-day.today {
            padding-right: 3.75pt;
        }

        [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-column] [class~=timetable-column-content] [class~=timetable-time] {
            font-size: 10px;
        }

        [class~=tiva-timetable] table[class~=timetable-month] td[class~=calendar-day], .tiva-timetable table.timetable-month td.calendar-day.today {
            padding-top: .010416667in;
        }

        [class~=tiva-timetable] [class~=timetable-month] [class~=calendar-daycounter], [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-axis] [class~=axis-item], .tiva-timetable .timetable-week .timetable-column .timetable-column-header {
            font-size: .125in;
        }

        .tiva-timetable .timetable-week.show-time .timetable-axis, [class~=tiva-timetable] [class~=timetable-week] [class~=timetable-axis] {
            padding-top: 27.75pt;
        }
    }
</style>