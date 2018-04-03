<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php

class vmPageNav {

    /** @var int The record number to start dislpaying from */
    var $limitstart = null;

    /** @var int Number of rows to display per page */
    var $limit = null;

    /** @var int Total number of rows */
    var $total = null;
    var $page = null;
    var $cur_page = null;
    var $previous_btn = true;
    var $last_btn = false;
    var $next_btn = true;
    var $first_btn = false;
    var $no_of_paginations = null;
    var $pre = null;
    var $start_loop = null;
    var $end_loop = null;
    var $nex = null;
    var $this_page = null;

    // Constructor for initializing the values
    function vmPageNav($total, $limitstart, $limit, $page, $form = '') {
        $this->total = intval($total);
        $this->page = $page;
        $this->limitstart = max($limitstart, 0);
        $this->limit = max($limit, 1);
        $this->form = $form;
    }

    /**
     * @return string The html for the limit # dropdown box
     */
    function getLimitBox() {
        global $lang;

        $html .= "<select name='pageno' class='combo' onchange='document.$this->form.submit();' size='1'>";
        for ($i = 5; $i <= 30; $i += 5) {
            if ($this->limit == $i) {
                $selected = "selected = 'selected'";
            } else {
                $selected = '';
            }
            $html .= "<option value='$i' " . $selected . " >$i</option>";
        }
        if ($this->limit == 50)
            $selected50 = "selected = 'selected'";
        else
            $selected50 = "";

        if ($this->limit == 100)
            $selected100 = "selected = 'selected'";
        else
            $selected100 = "";

        $html .= "<option value='50' " . $selected50 . " >50</option>";
        $html .= "<option value='100' " . $selected100 . " >100</option>";
        $html .= "</select>";
        $html .= "\n<input type=\"hidden\" name=\"limitstart\" value=\"$this->limitstart\" />";

        return $html;
    }

    /**
     * Display the html limit # dropdown box
     */
    function writeLimitBox() {
        echo $this->getLimitBox();
    }

    function writePagesCounter() {
        echo $this->getPagesCounter();
    }

    /**
     * @return string The html for the pages counter, eg, Results 1-10 of x
     */
    function getPagesCounter() {
        global $lang;
        $html = '';
        $from_result = $this->limitstart + 1;
        if ($this->limitstart + $this->limit < $this->total) {
            $to_result = $this->limitstart + $this->limit;
        } else {
            $to_result = $this->total;
        }
        if ($this->total > 0) {
            $html .= "<span class='fl results'>Page " . $from_result . " - " . $to_result . " of " . $this->total . '</span>';
        } else {
            
        }
        return $html;
    }

    /**
     * Writes the html for the pages counter, eg, Results 1-10 of x
     */
    function writePagesLinks() {
        echo $this->getPagesLinks();
    }

    /**
     * @return string The html links for pages, eg, previous, next, 1 2 3 ... x
     */
    function getPagesLinks() {
        global $lang;
        $html = '';
        $displayed_pages = 10;
        $total_pages = ceil($this->total / $this->limit);
        $this_page = ceil(($this->limitstart + 1) / $this->limit);
        $start_loop = (floor(($this_page - 1) / $displayed_pages)) * $displayed_pages + 1;
        if ($start_loop + $displayed_pages - 1 < $total_pages) {
            $stop_loop = $start_loop + $displayed_pages - 1;
        } else {
            $stop_loop = $total_pages;
        }

        if ($this_page > 1) {
            $page = ($this_page - 2) * $this->limit;
            $html .= "\n<a href=\"#beg\" title=\"first page\" onclick=\"javascript: document.$this->form.limitstart.value=0; document.$this->form.submit();return false;\"  class=\"brownlink floatleft pagenav first\">&nbsp;</a>";
            $html .= "\n<a href=\"#prev\" title=\"previous page\" onclick=\"javascript: document.$this->form.limitstart.value=$page; document.$this->form.submit();return false;\" class=\"brownlink floatleft pagenav prev\">&nbsp;</a>";
        } else {
            $html .= "\n<span class=\"pagenav firstoff\">&nbsp;</span>";
            $html .= "\n<span class=\"pagenav prevoff\">&nbsp;</span>";
        }

        for ($j = $start_loop; $j <= $stop_loop; $j++) {
            $page = ($j - 1) * $this->limit;
            if ($j == $this_page) {
                $html .= "\n<span class=\"pagenav floatleft fl\"> $j </span>";
            } else {
                $html .= "\n<a  class=\"brownlink floatleft fl\" href=\"#$j\" onclick=\"javascript: document.$this->form.limitstart.value=$page; document.$this->form.submit();return false;\"><strong>$j</strong>&nbsp;&nbsp;</a>";
            }
        }

        if ($this_page < $total_pages) {
            $page = $this_page * $this->limit;
            $end_page = ($total_pages - 1) * $this->limit;
            $html .= "\n<a href=\"#next\" title=\"next page\" onclick=\"javascript: document.$this->form.limitstart.value=$page; document.$this->form.submit();return false;\" class=\"brownlink floatleft pagenav next\">&nbsp;</a>";
            $html .= "\n<a href=\"#end\"  title=\"end page\" onclick=\"javascript: document.$this->form.limitstart.value=$end_page; document.$this->form.submit();return false;\" class=\"brownlink floatleft pagenav last\">&nbsp;</a>";
        } else {
            $html .= "\n<span class=\"pagenav nextoff\">&nbsp;</span>";
            $html .= "\n<span class=\"pagenav lastoff\">&nbsp;</span>";
        }
        return $html;
    }

    function getPagesLinks_Custom($str, $url) {
        global $lang;
        $html = '';
        $displayed_pages = 10;
        $total_pages = ceil($this->total / $this->limit);
        $this_page = ceil(($this->limitstart + 1) / $this->limit);
        $start_loop = ((floor(($this_page - 1) / $displayed_pages)) * $displayed_pages) + 1;
        if ($start_loop + $displayed_pages - 1 < $total_pages) {
            $stop_loop = $start_loop + $displayed_pages - 1;
        } else {
            $stop_loop = $total_pages;
        }

        $page = ($this_page) * $this->limit;

        if ($this_page < $total_pages) {
            $html = "<li class='next-posts' style='display:none'><a  href='" . $url . "?limitstart=" . $page . "&" . $str . "' ><strong>$this_page</strong>&nbsp;&nbsp;</a></li>";
        }

        return $html;
    }

    function getPagesLinks_Prev_Next($class = '') {
        $pagerhtml = "";
        $this->cur_page = $this->page;
        $this->page -= 1;
        $this->per_page = $this->limit;
        $this->previous_btn = true;
        $this->next_btn = true;
        $this->first_btn = false;
        $this->last_btn = false;
        $this->limitstart = $this->page * $this->per_page;
        $this->no_of_paginations = ceil($this->total / $this->per_page);

        if ($this->cur_page >= 7) {
            $this->start_loop = $this->cur_page - 3;
            if ($this->no_of_paginations > $this->cur_page + 3)
                $this->end_loop = $this->cur_page + 3;
            else if ($this->cur_page <= $this->no_of_paginations && $this->cur_page > $this->no_of_paginations - 6) {
                $this->start_loop = $this->no_of_paginations - 6;
                $this->end_loop = $this->no_of_paginations;
            } else {
                $this->end_loop = $this->no_of_paginations;
            }
        } else {
            $this->start_loop = 1;
            if ($this->no_of_paginations > 7)
                $this->end_loop = 7;
            else
                $this->end_loop = $this->no_of_paginations;
        }
        $pagerhtml = "";

        // FOR ENABLING THE FIRST BUTTON
        if ($this->first_btn && $this->cur_page > 1) {
            $pagerhtml .= "<li p='1' class='active'>First</li>";
        } else if ($this->first_btn) {
            $pagerhtml .= "<li p='1' class='inactive'>First</li>";
        }

        // FOR ENABLING THE PREVIOUS BUTTON
        if ($this->previous_btn && $this->cur_page > 1) {
            $this->pre = $this->cur_page - 1;
            $pagerhtml .= '<a href="javascript:;" p="' . $this->pre . '"  class="prev-btn active" ><span><img src="images/separator.png" alt="" title="" /></span></a>';
        } else if ($this->previous_btn) {
            $pagerhtml .= '<a href="javascript:;" class="prev-btn inactive"><span><img src="images/separator.png" alt="" title="" /></span></a>';
        }

        // TO ENABLE THE NEXT BUTTON  && $this->cur_page < $this->no_of_paginations
        if ($this->next_btn && $this->cur_page < $this->no_of_paginations) {
            $this->nex = $this->cur_page + 1;
            $pagerhtml .= '<a href="javascript:;" p="' . $this->nex . '" class="next-btn active"><span><img src="images/separator.png" alt="" title="" /></span></a>';
        } else if ($this->next_btn) {
            $pagerhtml .= '<a href="javascript:;" class="next-btn inactive"><span><img src="images/separator.png" alt="" title="" /></span></a>';
        }

        // TO ENABLE THE END BUTTON
        if ($this->last_btn && $this->cur_page < $this->no_of_paginations) {
            $pagerhtml .= "<li p='$this->no_of_paginations' class='active'>Last</li>";
        } else if ($last_btn) {
            $pagerhtml .= "<li p='$this->no_of_paginations' class='inactive'>Last</li>";
        }
        return $pagerhtml;
    }

    function getListFooter() {
        $html = '<table class="adminlist">';
        if ($this->total > $this->limit || $this->limitstart > 0) {
            $html .= '<tr><th colspan="3">';
            $html .= $this->getPagesLinks();
            $html .= '</th></tr>';
        }
        $html .= '<tr><td nowrap="true" width="48%" align="right">Display</td>';
        $html .= '<td>' . $this->getLimitBox() . '</td>';
        $html .= '<td nowrap="true" width="48%" align="left">' . $this->getPagesCounter() . '</td>';
        $html .= '</tr></table>';
        return $html;
    }

    /**
     * @param int The row index
     * @return int
     */
    function rowNumber($i) {
        return $i + 1 + $this->limitstart;
    }

    /**
     * @param int The row index
     * @param string The task to fire
     * @param string The alt text for the icon
     * @return string
     */
    function orderUpIcon($i, $condition = true, $task = 'orderup', $alt = 'Move Up', $page, $func) {
        global $mosConfig_live_site;
        if (($i > 0 || ($i + $this->limitstart > 0)) && $condition) {
            return '<a href="#reorder" onclick="return vm_listItemTask(\'cb' . $i . '\',\'' . $task . '\', \'$this->form\', \'' . $page . '\', \'' . $func . '\')" title="' . $alt . '">
                        <img src="' . $mosConfig_live_site . '/administrator/images/uparrow.png" width="12" height="12" border="0" alt="' . $alt . '" />
                </a>';
        } else {
            return '&nbsp;';
        }
    }

    /**
     * @param int The row index
     * @param int The number of items in the list
     * @param string The task to fire
     * @param string The alt text for the icon
     * @return string
     */
    function orderDownIcon($i, $n, $condition = true, $task = 'orderdown', $alt = 'Move Down', $page, $func) {
        global $mosConfig_live_site;
        if (($i < $n - 1 || $i + $this->limitstart < $this->total - 1) && $condition) {
            return '<a href="#reorder" onclick="return vm_listItemTask(\'cb' . $i . '\',\'' . $task . '\', \'$this->form\', \'' . $page . '\', \'' . $func . '\')" title="' . $alt . '">
                        <img src="' . $mosConfig_live_site . '/administrator/images/downarrow.png" width="12" height="12" border="0" alt="' . $alt . '" />
                </a>';
        } else {
            return '&nbsp;';
        }
    }

}

?>