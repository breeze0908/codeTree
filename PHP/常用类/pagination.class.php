<?php
/**
 * @file      : pagination.class.php
 *
 * @date      : 2017/2/23 16:50
 * @author    : Tan
 */

/**
 * @class:  pagination
 * @param:  $p_total - 总记录数
 *          $p_size  - 一页显示的记录数
 *          $p_page - 当前页
 *          $p_url - 获取当前的url
 *          
 * @desc  分页实现
 */
class pagination {

	private $p_total; //总记录数
	private $p_size; //一页显示的记录数
	private $p_page; //当前页
	private $p_page_count; //总页数
	private $p_i; //起头页数
	private $p_en; //结尾页数
	private $p_url; //获取当前的url
	/*
	     * $show_pages
	     * 页面显示的格式，显示链接的页数为2*$show_pages+1。
	     * 如$show_pages=2那么页面上显示就是[首页] [上页] 1 2 3 4 5 [下页] [尾页]
*/
	private $show_pages;

	public function __construct($p_total = 1, $p_size = 1, $p_page = 1, $p_url = '', $show_pages = 2) {
		$this->p_total      = $this->numeric($p_total);
		$this->p_size       = $this->numeric($p_size);
		$this->p_page       = $this->numeric($p_page);
		$this->p_page_count = ceil($this->p_total / $this->p_size);

		//链接
		if ($p_url) {
			$this->p_url = $p_url;
		} else {
			foreach ($_GET AS $gk => $gv) {
				if ($gk != "pagenum") {
					$gurl[] = $gk . "=" . @urlencode($gv);
				}
			}
			$gurl[] = 'pagenum={page}';
			if ($gurl AND is_array($gurl)) {
				$urls = htmlspecialchars(trim(implode("&", $gurl)));
			}
			$this->p_url = $_SERVER['PHP_SELF'] . "?" . $urls;
		}

		if ($this->p_total < 0) {
			$this->p_total = 0;
		}

		if ($this->p_page < 1) {
			$this->p_page = 1;
		}

		if ($this->p_page_count < 1) {
			$this->p_page_count = 1;
		}

		if ($this->p_page > $this->p_page_count) {
			$this->p_page = $this->p_page_count;
		}

		$this->limit = ($this->p_page - 1) * $this->p_size;
		$this->p_i   = $this->p_page - $show_pages;
		$this->p_en  = $this->p_page + $show_pages;
		if ($this->p_i < 1) {
			$this->p_en = $this->p_en + (1 - $this->p_i);
			$this->p_i  = 1;
		}
		if ($this->p_en > $this->p_page_count) {
			$this->p_i  = $this->p_i - ($this->p_en - $this->p_page_count);
			$this->p_en = $this->p_page_count;
		}
		if ($this->p_i < 1) {
			$this->p_i = 1;
		}

	}

	//检测是否为数字
	private function numeric($num) {
		if (strlen($num)) {
			if (!preg_match("/^[0-9]+$/", $num)) {
				$num = 1;
			} else {
				$num = substr($num, 0, 11);
			}
		} else {
			$num = 1;
		}
		return $num;
	}

	//地址替换
	private function page_replace($page) {
		return str_replace("{page}", $page, $this->p_url);
	}

	//首页
	private function p_home() {
		if ($this->p_page != 1) {
			return '<li class="paginate_button previous"><a href="' . $this->page_replace(1) . '" title="首页">首页</a></li>';
		} else {
			return '<li class="paginate_button previous disabled"><a href="#">首页</a></li>';
		}
	}

	//上一页
	private function p_prev() {
		if ($this->p_page != 1) {
			return '<li class="paginate_button"><a href="' . $this->page_replace($this->p_page - 1) . '" title="上一页">上一页</a></li>';
		} else {
			return '<li class="paginate_button disabled"><a href="#">上一页</a></li>';
		}
	}

	//下一页
	private function p_next() {
		if ($this->p_page != $this->p_page_count) {
			return '<li class="paginate_button"><a href="' . $this->page_replace($this->p_page + 1) . '" title="下一页">下一页</a></li>';
		} else {
			return '<li class="paginate_button disabled"><a href="#">下一页</a></li>';
		}
	}

	//尾页
	private function p_last() {
		if ($this->p_page != $this->p_page_count) {
			return '<li class="paginate_button next"><a href="' . $this->page_replace($this->p_page_count) . '" title="尾页">尾页</a></li>';
		} else {
			return '<li class="paginate_button next disabled"><a href="#">尾页</a></li>';
		}
	}

	//输出
	public function output($id = 'page') {
		$str = '<ul class="pagination pull-right" id="' . $id . '">';
		$str .= $this->p_home();
		$str .= $this->p_prev();
		if ($this->p_i > 1) {
			$str .= '<li class="paginate_button"><a href="#">...</a></li>';
		}
		for ($i = $this->p_i; $i <= $this->p_en; $i++) {
			if ($i == $this->p_page) {
				$str .= '<li class="paginate_button active"><a href="' . $this->page_replace($i) . '">' . $i . '</a></li>';
			} else {
				$str .= '<li class="paginate_button"><a href="' . $this->page_replace($i) . '">' . $i . '</a></li>';
			}
		}
		if ($this->p_en < $this->p_page_count) {
			$str .= '<li class="paginate_button"><a href="#">...</a></li>';
		}
		$str .= $this->p_next();
		$str .= $this->p_last();
		$str .= "</ul>";
		/*$str .= "<p class='pageRemark'>共<b>" . $this->p_page_count ."</b>页<b>" . $this->p_total . "</b>条数据</p>";
        $str .= "</div>";*/
		return $str;
	}

	/**
	 * 当前分页第一条记录
	 * @return float|int
	 */
	public function first() {
		return ($this->p_page - 1) * $this->p_size;
	}

	/**
	 * @return int|string
	 */
	public function length() {
		return $this->p_size;
	}
}