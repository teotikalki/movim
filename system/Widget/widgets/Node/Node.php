<?php

/**
 * @package Widgets
 *
 * @file Node.php
 * This file is part of MOVIM.
 *
 * @brief The items of a node
 *
 * @author Timothée	Jaussoin <edhelas_at_gmail_dot_com>
 *
 * @version 1.0
 * @date 20 October 2010
 *
 * Copyright (C)2010 MOVIM project
 *
 * See COPYING for licensing information.
 */

class Node extends WidgetCommon
{
    function WidgetLoad()
    {
		$this->registerEvent('stream', 'onStream');
    }
    
    function onStream($id) {
        $html = $this->prepareGroup($id[0], $id[1]);
        
        if($html == '') 
            $html = t("Your feed cannot be loaded.");
        RPC::call('movim_fill', 'node', RPC::cdata($html));
        RPC::commit();
    }

    function ajaxGetItems($server, $node)
    {
        $r = new moxl\GroupNodeGetItems();
        $r->setTo($server)
          ->setNode($node)
          ->request();
    }
    
    function prepareGroup($serverid, $groupid) {
        $title = '
            <a href="?q=server&s='.$serverid.'">
                '.$serverid.'
            </a> >
            <a href="?q=node&s='.$serverid.'&n='.$groupid.'">
                '.$groupid.'
            </a> >
            '.t('Posts');
        
        $pd = new modl\PostDAO();
        $posts = $pd->getGroup($serverid, $groupid);
        
        $html = $title;
        foreach($posts as $post) {
            $html .= $this->printPost($post);
        }
        
        return $html;
    }

    function build()
    {
    ?>
        <div class="tabelem protect red" id="node" title="<?php echo t('Posts');?>">
            <?php echo $this->prepareGroup($_GET['s'], $_GET['n']); ?>
            <script type="text/javascript"><?php echo $this->genCallAjax('ajaxGetItems', "'".$_GET['s']."'", "'".$_GET['n']."'"); ?></script>
        </div>
    <?php
    }
}