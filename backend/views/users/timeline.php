<?php

/* @var $events */

$this->title = "Журнал задач";
?>
<div class="content-wrapper" style="padding-top: 20px">
    <section class="content-header">
        <h1>
           Журнал задач
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">

            <ul class="timeline timeline-inverse">
                <?php
                foreach ($events as $event) {
                    echo $event['event'];
                }
                ?>
            </ul>
        </div>
    </section>
</div>
