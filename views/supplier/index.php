<?php

/** @var yii\web\View $this */
/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var \app\models\form\SupplierFilterForm $model */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\LinkPager;
use yii\grid\CheckboxColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'Suppliers';
$pageSize = $dataProvider->getPagination()->pageSize;
$totalCount = $dataProvider->getTotalCount();
?>
  <div class="grid-top mb-3">
    <button id="export" type="button" disabled class="btn btn-primary" data-toggle="modal" data-target="#cloModel">Export CSV</button>
    <div class="post-search">
        <?php $form = ActiveForm::begin([
            'action'                 => ['index'],
            'method'                 => 'get',
            'layout'                 => ActiveForm::LAYOUT_INLINE,
            'enableClientValidation' => false
        ]); ?>

        <?= $form->field($model, 'id')->textInput() ?>

        <?= $form->field($model, 'name')->textInput() ?>

        <?= $form->field($model, 'code')->textInput() ?>

        <?= $form->field($model, 'status', ['enableLabel' => false])->dropdownList([
          'all'  => 'Status: All',
          'ok'   => 'OK',
          'hold' => 'Hold'
        ], ['placeholder' => 'Status']) ?>

      <div class="form-group">
          <?= Html::submitButton('Search', ['class' => 'btn btn-outline-primary']) ?>
          <?= Html::a('Reset', '/supplier/index', ['class' => 'btn btn-outline-secondary ml-1']) ?>
      </div>

        <?php ActiveForm::end(); ?>
    </div>
  </div>
  <div id="selectAll" class="alert alert-warning" style="display: none" role="alert">
    All <?= $pageSize ?> suppliers on this page have been selected. <a href="#" class="alert-link">Select all suppliers that match this search</a>
  </div>
  <div id="clearSelect" class="alert alert-success" style="display: none" role="alert">
    All <?= $totalCount ?> suppliers in this search have been selected. <a href="#" class="alert-link">Clear selection</a>
  </div>
  <div class="site-grid">
      <?= GridView::widget([
          'id'           => 'suppliers',
          'dataProvider' => $dataProvider,
          'layout'       => "{items}<div class='grid-bottom clearfix'>{summary}{pager}</div>",
          'columns'      => [
              [
                  'class'           => CheckboxColumn::class,
                  'headerOptions'   => [
                      'class' => 'checkbox-item'
                  ],
                  'checkboxOptions' => function ($model, $key, $index, $column) {
                      return ['value' => $model->id, 'class' => 'checkbox-ids'];
                  },
                  'contentOptions'  => ['width' => '3%'],
              ],

              'id'     => [
                  'attribute'      => 'id',
                  'enableSorting'  => true,
                  'contentOptions' => ['width' => '7%'],
              ],
              'name'   => [
                  'attribute'      => 'name',
                  'contentOptions' => ['width' => '50%'],
              ],
              'code'   => [
                  'attribute'      => 'code',
                  'contentOptions' => ['width' => '20%'],
              ],
              'status' => [
                  'attribute'      => 't_status',
                  'contentOptions' => ['width' => '20%'],
                  'format'         => 'html',
                  'value'          => function ($data) {
                      if ($data->t_status === 'ok') {
                          return '<span class="badge badge-success">OK</span>';
                      }
                      if ($data->t_status === 'hold') {
                          return '<span class="badge badge-warning">Hold</span>';
                      }
                      return '<span class="badge badge-secondary">' . ucfirst($data->t_status) . '</span>';
                  },
              ],
          ],
          'pager'        => [
              'class'          => LinkPager::class,
              'firstPageLabel' => 'First',
              'lastPageLabel'  => 'Last',
          ]

      ]) ?>
  </div>
  <div class="modal fade" id="cloModel" tabindex="-1" aria-labelledby="colModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="colModalLabel">Select export columns</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div id="columnsList" class="modal-body">
        <?php foreach ($model as $name => $value) { ?>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" value="<?= $name ?>" checked <?= $name === 'id' ? 'disabled' : '' ?> name="columns"
                   id="columns_<?= $name ?>">
            <label class="form-check-label" for="columns_<?= $name ?>"><?= ucfirst($name) ?></label>
          </div>
        <?php } ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal" id="exportDo">Export</button>
        </div>
      </div>
    </div>
  </div>
<?php
$this->registerJs(<<< JS
var pageSize = ${pageSize};
var totalCount = ${totalCount};

$(function (){
  $('#supplier-id').popover({
    trigger: 'focus',
    content: 'input >10, match all id greater than 10, also support: <12, >=100, <=6, =5, e.g...',
    placement: 'left'
  });
  
  var keys = [];
  $('.checkbox-ids, .select-on-check-all').change(function () {
    keys = $('#suppliers').yiiGridView('getSelectedRows');
    $('#export').prop('disabled', keys.length === 0);
    if (keys.length === pageSize && totalCount > pageSize) {
      $('#selectAll').show();
    } else {
      $('#selectAll').hide();
    }
    $('#clearSelect').hide();
  })
  $('#selectAll > a').click(function () {
    keys = ['all'];
    $('#clearSelect').show();
    $('#selectAll').hide();
  })
  $('#clearSelect > a').click(function () {
    $('.select-on-check-all').click();
  })
  $("#exportDo").click(function () {
    var query = window.location.search;
    var columns = [];
    $('#columnsList input[name="columns"]:checked').each(function () {
      columns.push($(this).val())
    })
    window.location.href = '/suppliers/export' + (query ? query + '&' : '?') 
    + 'columns=' + columns.join(',') 
    + '&keys=' + keys.join(',');
  })
})

JS, View::POS_END);
?>