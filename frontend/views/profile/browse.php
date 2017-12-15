<?php

use common\models\profile\Profile;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\jui\SliderInput;
use yii\web\JsExpression;

/* @var $this yii\web\View */
Url::remember();
$stateSelected = NULL;
$citySelected = NULL;
$this->title = 'Browse';
?>
<div class="wrap browse">
    <div class="container">
        <h1><?= $this->title ?></h1>
    </div>
</div>
<div class="clearbrowse"></div> 
    
    <div class="wrap">
        <div class="container alert-browse">

        <?php Pjax::begin(); ?>
            <div class="row">
                <div class="col-xs-12 col-sm-3">

                    <!-- =============================== Spatial Search =============================== -->
                   <div class="left-block">
                        <?php $form = ActiveForm::begin([
                            'options' => [
                                'data' => ['pjax' => false],
                                'data-pjax'=> '0',
                            ]
                        ]); ?>
                       <div id="slider-info">Within <span id="slider-value"><?= $browseModel->distance .' miles' ?></span></div>
                        <?= $form->field($browseModel, 'distance')->widget(SliderInput::classname(), [
                                'clientOptions' => [
                                    //'range' => 'min',
                                    'min' => 5,
                                    'max' => 250,
                                    'step' => 5,
                                    'slide' => new JsExpression('function( event, ui ) {
                                            $("#slider-value").html(ui.value+" miles");
                                            }'),
                                ],
                            ]);
                        ?>
                        <?= $form->field($browseModel, 'location', ['enableLabel' => false])->textInput(['class' => 'form-control', 'placeholder' => 'city, state', 'id' => 'gplaces']) ?>
                        <div class="form-group">
                            <?= Html::submitButton(HTML::icon('search').' Search', [
                                'method' => 'POST',
                                'class' => 'btn btn-primary',
                                'name' => 'submit',
                            ]) ?>
                            <?= Html::submitButton(HTML::icon('remove').' Reset', [
                                'method' => 'POST',
                                'class' => 'btn btn-primary pull-right',
                                'name' => 'clear',
                            ]) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>

                    <!-- =============================== Map =============================== -->
                    <?php if($center && !empty($_SESSION['spatial']['distance']) && !empty($_SESSION['spatial']['location'])) { ?>
                        <div id="map" class="left-block"></div>
                        <script type="text/javascript">
                            var mapCoords = <?= $center ?>;
                            var locations = <?= json_encode($markers); ?>;
                            var radius = <?= $_SESSION['spatial']['distance']; ?>;
                        </script>
                    <?php  } else { ?>

                    <!-- =============================== Country, State, City =============================== -->
                    <div class="left-block">

                        <!-- =============================== Country =============================== -->
                        <h4>Country</h4>
                        <?php $countries = $resultSet->getFacetSet()->getFacet('f_country');
                        $i = 0;
                        foreach ($countries as $countryPivot) {
                            $i++;
                            if ($i > 3 && $more['country'] == 1) {continue;}
                            if (($countryCount = $countryPivot->getCount()) > 0) {
                                $country = $countryPivot->getValue();
                                $countryConstraint = 'f_country:"' . $country . '"';
                                if (isset($fqs['country']['country']) && $countrySelected = ($fqs['country']['country'] == $countryConstraint)) {
                                    print('<b>&#187; ' . Html::a($country . ' (' . $countryCount . ')', ['facet/facet', 'constraint' => $countryConstraint . '+u', 'cat' => 'country', 'tabId' => '+tabId+']) . '</b><br/>');
                    echo '</div>';
                    echo $fqs['country']['country'] == 'f_country:"United States"' ?
                        '<div class="left-block states">' :
                        '<div class="left-block">';
                                # =============================== State ===============================
                                    echo '<h4>State or Region</h4>';
                                    $j = 0;
                                    $j1 = 0;    
                                    foreach ($countryPivot->getPivot() as $statePivot) {
                                        $j++;
                                        if ($j > 3 && $more['state'] == 1) {continue;}
                                        if (($stateCount = $statePivot->getCount()) > 0) {
                                            $j1++;
                                            $state = $statePivot->getValue();
                                            $stateConstraint = 'f_state:"' . $state . '"';
                                            if (isset($fqs['country']['state']['state']) && $stateSelected = ($fqs['country']['state']['state'] == $stateConstraint)) {
                                                print('<b>&#187; ' . Html::a($state . ' (' . $stateCount . ')', ['facet/facet', 'constraint' => $stateConstraint . '+u', 'cat' => 'state']) . '</b><br/>');
                    echo '</div>';
                                        # =============================== City ===============================
                    echo '<div class="left-block">';
                                                echo '<h4>City</h4>';
                                                $k = 0;
                                                foreach ($statePivot->getPivot() as $cityPivot) {
                                                    $k++;
                                                    if ($k > 3 && $more['city'] == 1) {continue;}
                                                    if (($cityCount = $cityPivot->getCount()) > 0) {
                                                        $city = $cityPivot->getValue();
                                                        $cityConstraint = 'f_city:"' . $city . '"';
                                                        (isset($fqs['country']['state']['city']) && $citySelected = ($fqs['country']['state']['city'] == $cityConstraint)) ?
                                                                print('<b>&#187; ' . Html::a($city . ' (' . $cityCount . ')', ['facet/facet', 'constraint' => $cityConstraint . '+u', 'cat' => 'city']) . '</b><br/>') :
                                                                print(Html::a($city . ' (' . $cityCount . ')', ['facet/facet', 'constraint' => $cityConstraint . '+s', 'cat' => 'city']) . '<br/>');
                                                    } else {break;}
                                                }
                                                if (!isset($citySelected) && $k > 3) {
                                                    $more['city'] == 1 ?
                                                        print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'city', 'toggle' => true]) . '<br/><br/>') :
                                                        print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'city', 'toggle' => true]) . '<br/><br/>');
                                                }
                                        # =============================== End City ===============================
                                            } else {
                                                if ($fqs['country']['country'] == 'f_country:"United States"') {
                                                    print(Html::a($state . ' (' . $stateCount . ')', ['facet/facet', 'constraint' => $stateConstraint . '+s', 'cat' => 'state']));
                                                    echo $j%3 == 0 ? '<br/>' : '&nbsp&nbsp';
                                                } else {
                                                    print(Html::a($state . ' (' . $stateCount . ')', ['facet/facet', 'constraint' => $stateConstraint . '+s', 'cat' => 'state']) . '<br/>');
                                                }
                                            }
                                        } else {break;}
                                    }
                                    if (!isset($stateSelected) && $j > 3) {
                                        echo $j1%3 == 0 ? NULL : '<br/>';
                                        $more['state'] == 1 ?
                                            print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'state', 'toggle' => true], ['class' => 'nowidth']) . '') :
                                            print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'state', 'toggle' => true], ['class' => 'nowidth']) . '');
                                    }
                                # =============================== End State ===============================
                                } else {
                                    print(Html::a($country . ' (' . $countryCount . ')', ['facet/facet', 'constraint' => $countryConstraint . '+s', 'cat' => 'country', 'tabId' => '"+tabId+"']) . '<br/>');
                                }
                            } else {break;}
                        }
                        if (!isset($countrySelected) && $i > 3) {
                            $more['country'] == 1 ?
                                print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'country', 'toggle' => true]) . '') :
                                print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'country', 'toggle' => true]) . '');
                        } ?>
                    </div>
                    <!-- =============================== End Country =============================== -->

                    <?php } ?>
        
                    <!-- =============================== Profile Type =============================== -->
                    <div class="left-block">
                        <h4>Profile Type</h4>
                        <?php $types = $resultSet->getFacetSet()->getFacet('type');
                        $i = 0;
                        foreach ($types as $type => $count) {
                            $i++;
                            if ($i > 3 && $more['type'] == 1) {continue;}
                            $constraint = 'f_type:"' . $type . '"';
                            if (isset($fqs['type']['type']) && $typeSelected = ($constraint == $fqs['type']['type'])) {
                                print('<b>&#187; ' . Html::a($type . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'type']) . '</b><br/>');
                            } else {
                                print(Html::a($type . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'type']) . '<br/>');
                            }
                        }
                        if (!isset($typeSelected) && $i > 3) {
                            $more['type'] == 1 ?
                                print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'type', 'toggle' => true]) . '') :
                                print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'type', 'toggle' => true]) . '');
                        } ?>
                    </div>

                    <!-- =============================== Subcategories for type =============================== -->
                    <?php if (isset($fqs['type']['type'])) {
                        $type = explode(':', $fqs['type']['type']);
                        echo '<div class="left-block">';
                            switch ($type[1]) {

                             # =============================== Church programs ===============================
                                case '"Church"':
                                    echo '<h4>Programs</h4>';
                                    $programs = $resultSet->getFacetSet()->getFacet('program');
                                    $i = 0;
                                    foreach ($programs as $program => $count) {
                                        $i++;
                                        if ($i > 3 && $more['program'] == 1) {continue;}
                                        $constraint = 'f_pg_org_name:"' . $program . '"';
                                        if (isset($fqs['type']['program']) && $specialSelected = ($constraint == $fqs['type']['program'])) {
                                            print('<b>&#187; ' . Html::a($program . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'program']) . '</b><br/>');
                                        } else {
                                            print(Html::a($program . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'program']) . '<br/>');
                                        }
                                    }
                                    if (!isset($specialSelected) && $i > 3) {
                                        $more['program'] == 1 ?
                                            print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'program', 'toggle' => true]) . '') :
                                            print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'program', 'toggle' => true]) . '');
                                    }
                        echo '</div>';
                                    break;
                    
                            # =============================== Special Ministry profile tags ===============================
                                case '"Special Ministry"':
                                    echo '<h4>Tags</h4>';
                                    $tags = $resultSet->getFacetSet()->getFacet('tag');
                                    $i = 0;
                                    foreach ($tags as $tag => $count) {
                                        $i++;
                                        if ($i > 3 && $more['tag'] == 1) {continue;}
                                        $constraint = 'f_tag:"' . $tag . '"';
                                        if (isset($fqs['type']['tag']) && $specialSelected = ($constraint == $fqs['type']['tag'])) {
                                            print('<b>&#187; ' . Html::a($tag . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'tag']) . '</b><br/>');
                                        } else {
                                            print(Html::a($tag . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'tag']) . '<br/>');
                                        }
                                    }
                                    if (!isset($specialSelected) && $i > 3) {
                                        $more['tag'] == 1 ?
                                            print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'tag', 'toggle' => true]) . '') :
                                            print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'tag', 'toggle' => true]) . '');
                                    }
                        echo '</div>';
                                    break;

                            # =============================== Missionary status, field and agency ===============================
                                case '"Missionary"':
                                    echo '<h4>Missionary Type</h4>';
                                    $sub_types = $resultSet->getFacetSet()->getFacet('sub_type');
                                    $i = 0;
                                    foreach ($sub_types as $sub_type => $count) {
                                        $i++;
                                        if ($i > 3 && $more['sub_type'] == 1) {continue;}
                                        $constraint = 'f_sub_type:"' . $sub_type . '"';
                                        if (isset($fqs['type']['sub_type']) && $sub_typeSelected = ($constraint == $fqs['type']['sub_type'])) {
                                            print('<b>&#187; ' . Html::a($sub_type . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'sub_type']) . '</b><br/>');
                                        } else {
                                            print(Html::a($sub_type . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'sub_type']) . '<br/>');
                                        }
                                    }
                                    if (!isset($sub_typeSelected) && $i > 3) {
                                        $more['sub_type'] == 1 ?
                                            print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'sub_type', 'toggle' => true]) . '') :
                                            print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'sub_type', 'toggle' => true]) . '');
                                    }
                        echo '</div>';
                        echo '<div class="left-block">';

                                    echo '<h4>Status</h4>';
                                    $statuses = $resultSet->getFacetSet()->getFacet('miss_status');
                                    foreach ($statuses as $status => $count) {
                                        $i++;
                                        $constraint = 'f_miss_status:"' . $status . '"';
                                        if (isset($fqs['type']['miss_status']) && $selected = ($constraint == $fqs['type']['miss_status'])) {
                                            print('<b>&#187; ' . Html::a($status . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'miss_status']) . '</b><br/>');
                                        } else {
                                            print(Html::a($status . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'miss_status']) . '<br/>');
                                        } 
                                    }
                        echo '</div>';
                        echo '<div class="left-block">';

                                    echo '<h4>Field</h4>';
                                    $fields = $resultSet->getFacetSet()->getFacet('miss_field');
                                    $i = 0;
                                    foreach ($fields as $field => $count) {
                                        $i++;
                                         if ($i > 3 && $more['miss_field'] == 1) {continue;}
                                        $constraint = 'f_miss_field:"' . $field . '"';
                                        if (isset($fqs['type']['miss_field']) && $fieldSelected = ($constraint == $fqs['type']['miss_field'])) {
                                            print('<b>&#187; ' . Html::a($field . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'miss_field']) . '</b><br/>');
                                        } else {
                                            print(Html::a($field . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'miss_field']) . '<br/>');
                                        }
                                    }
                                    if (!isset($fieldSelected) && $i > 3) {
                                        $more['miss_field'] == 1 ?
                                            print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'miss_field', 'toggle' => true]) . '') :
                                            print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'miss_field', 'toggle' => true]) . '');
                                    }
                        echo '</div>';
                        echo '<div class="left-block">';

                                    echo '<h4>Mission Agency</h4>';
                                    $agcys = $resultSet->getFacetSet()->getFacet('miss_agcy');
                                    $i = 0;
                                    foreach ($agcys as $agcy => $count) {
                                        $i++;
                                        if ($i > 3 && $more['miss_agcy'] == 1) {continue;}
                                        $constraint = 'f_miss_agcy:"' . $agcy . '"';
                                        if (isset($fqs['type']['miss_agcy']) && $agcySelected = ($constraint == $fqs['type']['miss_agcy'])) {
                                            print('<b>&#187; ' . Html::a($agcy . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'miss_agcy']) . '</b><br/>');
                                        } else {
                                            print(Html::a($agcy . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'miss_agcy']) . '<br/>');
                                        }
                                    }
                                    if (!isset($agcySelected) && $i > 3) {
                                        $more['miss_agcy'] == 1 ?
                                            print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'miss_agcy', 'toggle' => true]) . '') :
                                            print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'miss_agcy', 'toggle' => true]) . '');
                                    }
                        echo '</div>';
                                    break;

                            # =============================== School levels ===============================
                                case '"School"':
                                    echo '<h4>School Levels</h4>';
                                    $levels = $resultSet->getFacetSet()->getFacet('level');
                                    $i = 0;
                                    foreach ($levels as $level => $count) {
                                        $i++;
                                        if ($i > 3 && $more['level'] == 1) {continue;}
                                        $constraint = 'f_level:"' . $level . '"';
                                        if (isset($fqs['type']['level']) && $levelSelected = ($constraint == $fqs['type']['level'])) {
                                            print('<b>&#187; ' . Html::a($level . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'level']) . '</b><br/>');
                                        } else {
                                            print(Html::a($level . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'level']) . '<br/>');
                                        }
                                    }
                                    if (!isset($levelSelected) && $i > 3) {
                                        $more['level'] == 1 ?
                                            print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'level', 'toggle' => true]) . '') :
                                            print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'level', 'toggle' => true]) . '');
                                    }
                        echo '</div>';
                                    break;

                            # =============================== Pastor sub-type (e.g. senior, associate, etc.) ===============================
                                case '"Pastor"':
                                    echo '<h4>Pastor Type</h4>';
                                    $sub_types = $resultSet->getFacetSet()->getFacet('sub_type');
                                    $i = 0;
                                    foreach ($sub_types as $sub_type => $count) {
                                        $i++;
                                        if ($i > 3 && $more['sub_type'] == 1) {continue;}
                                        $constraint = 'f_sub_type:"' . $sub_type . '"';
                                        if (isset($fqs['type']['sub_type']) && $sub_typeSelected = ($constraint == $fqs['type']['sub_type'])) {
                                            print('<b>&#187; ' . Html::a($sub_type . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'sub_type']) . '</b><br/>');
                                        } else {
                                            print(Html::a($sub_type . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'sub_type']) . '<br/>');
                                        }
                                    }
                                    if (!isset($sub_typeSelected) && $i > 3) {
                                        $more['sub_type'] == 1 ?
                                            print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'sub_type', 'toggle' => true]) . '') :
                                            print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'sub_type', 'toggle' => true]) . '');
                                    }
                        echo '</div>';
                                    break;

                            # =============================== Staff profile titles ===============================
                                case '"Staff"':
                                    echo '<h4>Staff Title</h4>';
                                    $titles = $resultSet->getFacetSet()->getFacet('title');
                                    $i = 0;
                                    foreach ($titles as $title => $count) {
                                        $i++;
                                        if ($i > 3 && $more['title'] == 1) {continue;}
                                        $constraint = 'f_title:"' . $title . '"';
                                        if (isset($fqs['type']['title']) && $titleSelected = ($constraint == $fqs['type']['title'])) {
                                            print('<b>&#187; ' . Html::a($title . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'title']) . '</b><br/>');
                                        } else {
                                            print(Html::a($title . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'title']) . '<br/>');
                                        }
                                    }
                                    if (!isset($titleSelected) && $i > 3) {
                                        $more['title'] == 1 ?
                                            print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'title', 'toggle' => true]) . '') :
                                            print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'title', 'toggle' => true]) . '');
                                    }
                        echo '</div>';
                                    break;

                                default:
                        echo '</div>';
                                   break;
                            }
                    } ?>

                    <?php if (isset($type) && (
                        $type[1] == '"Church"' || 
                        $type[1] == '"Pastor"' || 
                        $type[1] == '"Missionary"' || 
                        $type[1] == '"Evangelist"')) { ?>
                        <!-- =============================== Bible =============================== -->
                        <div class="left-block">
                            <h4>Bible</h4>
                            <?php $bibles = $resultSet->getFacetSet()->getFacet('bible');
                            $i = 0;
                            foreach ($bibles as $value => $count) {
                                $i++;
                                if ($i > 3 && $more['bible'] == 1) {continue;}
                                $constraint = 'f_bible:"' . $value . '"';
                                if (isset($fqs['type']['bible']) && $selected = ($constraint == $fqs['type']['bible'])) {
                                    print('<b>&#187; ' . Html::a($value . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'bible']) . '</b><br/>');
                                } else {
                                    print(Html::a($value . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'bible']) . '<br/>');
                                }
                            }
                            if (!isset($selected) && $i > 3) {
                                $more['bible'] == 1 ?
                                    print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'bible', 'toggle' => true]) . '') :
                                    print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'bible', 'toggle' => true]) . '');
                            } ?>
                        </div>

                        <!-- =============================== Worship =============================== -->
                        <div class="left-block">
                            <h4>Worship</h4>
                            <?php $worship_style = $resultSet->getFacetSet()->getFacet('worship_style');
                            $i = 0;
                            foreach ($worship_style as $value => $count) {
                                $i++;
                                if ($i > 3 && $more['worship_style'] == 1) {continue;}
                                $constraint = 'f_worship_style:"' . $value . '"';
                                if (isset($fqs['type']['worship_style']) && $selected = ($constraint == $fqs['type']['worship_style'])) {
                                    print('<b>&#187; ' . Html::a($value . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'worship_style']) . '</b><br/>');
                                } else {
                                    print(Html::a($value . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'worship_style']) . '<br/>');
                                }
                            }
                            if (!isset($selected) && $i > 3) {
                                $more['worship_style'] == 1 ?
                                    print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'worship_style', 'toggle' => true]) . '') :
                                    print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'worship_style', 'toggle' => true]) . '');
                            } ?>
                        </div>

                        <!-- =============================== Polity =============================== -->
                        <div class="left-block">
                            <h4>Church Government</h4>
                            <?php $polity = $resultSet->getFacetSet()->getFacet('polity');
                            $i = 0;
                            foreach ($polity as $value => $count) {
                                $i++;
                                if ($i > 3 && $more['polity'] == 1) {continue;}
                                $constraint = 'f_polity:"' . $value . '"';
                                if (isset($fqs['type']['polity']) && $selected = ($constraint == $fqs['type']['polity'])) {
                                    print('<b>&#187; ' . Html::a($value . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+u', 'cat' => 'polity']) . '</b><br/>');
                                } else {
                                    print(Html::a($value . ' (' . $count . ')', ['facet/facet', 'constraint' => $constraint . '+s', 'cat' => 'polity']) . '<br/>');
                                }
                            }
                            if (!isset($selected) && $i > 3) {
                                $more['polity'] == 1 ?
                                    print(Html::a(HTML::icon('triangle-bottom') . ' More', ['facet/facet', 'constraint' => false, 'cat' => 'polity', 'toggle' => true]) . '') :
                                    print(Html::a(HTML::icon('triangle-top') . ' Fewer', ['facet/facet', 'constraint' => false, 'cat' => 'polity', 'toggle' => true]) . '');
                            } ?>
                        </div>
                    <?php } ?>
                </div>

                <!-- =============================== Browse Results =============================== -->

                <div class="col-xs-12 col-sm-9">

                    <?php if (empty($fqs) && (empty($browseModel->distance) || empty($browseModel->location))) {
                        echo '<div class="alert alert-cat" role="alert"><h3>' . Html::icon('hand-left') . ' Choose a category to begin browsing.</h3></div>';
                    } elseif ($resultSet->getNumFound() == 0) {
                       echo '<div class="alert alert-info" role="alert"><h4>No listings found.  Try a larger search radius.</h4></div>';
                    } else {
                        echo ListView::widget([
                            'dataProvider' => $dataProvider,
                            'showOnEmpty' => true,
                            'emptyText' => 'testing',
                            'itemView' => '_browseResults',
                            'itemOptions' => ['class' => 'item-bordered'],
                            'layout' => '<div class="summary-row hidden-print clearfix">{summary}</div>{items}{pager}',
                        ]); 

                    }?>
                </div>
            </div>
        </div>

        <?php Pjax::end(); ?>
                
    </div>

</div>

<?php
$script = <<< JS
$(document).ready(function() {
    //setTabId();
    $(document).on('pjax:end', function() {
      initMap();
    })
});
JS;
$this->registerJs($script);
?>
<script type="text/javascript">
    var tabId, placeSearch, autocomplete;
    var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name'
    };

    function initAutocomplete() {
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('gplaces')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
    }

    function fillInAddress() {
    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();

    $('#gplaces').val(place.formatted_address);
    }

    function loadMap() {
        if (typeof mapCoords === 'undefined') return false;
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: mapCoords
        });
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
              position: new google.maps.LatLng(locations[i][1], locations[i][2]),
              map: map,
              data: {
                name: locations[i][0]
              }
            });
            marker.addListener('click', function() {
              if(!this.infoWindow) {
                this.infoWindow = new google.maps.InfoWindow({
                  content: this.data.name
                });
              }
              this.infoWindow.open(map,this);
            })
        }
        // map.data.setStyle(function(feature) {
        //     return {
        //         icon: getCircle(radius)
        //     };
        // });
    }

    // function getCircle(radius) {
    //     return {
    //       path: google.maps.SymbolPath.CIRCLE,
    //       fillColor: 'red',
    //       fillOpacity: .2,
    //       scale: radius,
    //       strokeColor: 'white',
    //       strokeWeight: .5
    //     };
    //   }


    function initMap() {
        if ($('#map').length > 0) loadMap();
        initAutocomplete();
    }

    // function setTabId() {
        // var tabId = sessionStorage.tabHash = Math.random();
    // }

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAJAUkH21aE3XnCYSgT0v5HHZpoupZ0Nz4&libraries=places&callback=initMap"
        async defer></script>
