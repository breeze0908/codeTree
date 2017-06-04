title: mac
speaker: Zyj
url: https://github.com/ksky521/nodePPT
transition: cards
files: /js/demo.js,/css/demo.css

[slide]

# mac

[slide]

## MAC的优点

* 多个屏幕，打开多个软件，切屏爽 {:&.rollIn}
* 快捷方式多，触摸板灵活，部分场景可抛弃鼠标
* 搜索-快速打开软件（Alfred）
* 长时间不用关机
* 自带一些开发软件（php python）
* 轻便
* retina屏幕
* 待机时间长
* 用起来不像window卡
* 没弹窗
* 界面清洁
* 网页单词右键选中直接翻译
* 日历挺好用

[slide]

## 开发环境

* MAC+一转三HDMI外界显示器+teamviewer+widonws
* 如何打造王自如同款办公室(http://www.zealer.com/series/1)

## MAC的缺点

* 新版MAC键盘刚开始不适应 - 适应了就好了 {:&.rollIn}
* bar很容易误触摸
* 大部分软件收费，破解容被黑，或者有问题（限免，群）
* 也是会死机的
* flash播放器导致发热（flash快死了，而且我并没有觉得太热）
* 备忘录 无缘无故记录全没了（time machine）
* 好像对游戏支持不太好（同事）

[slide]
## 编辑器

* Sublime Text {:&.rollIn}
* Zend Studio(占内存)
* PhpStorm(占内存)
* Textwrangler : 类似于nodepad++ ,可以在versions中的对比工具中选用这个（不太好用）
* macvim

[slide]

##虚拟机软件

* VirtualBox {:&.rollIn}
* arallels Desktop（收费）
* crossover window模拟机（缺点是字太模糊）
* VMware Workstation（收费）

[slide]
##远程软件

* 远程桌面连接 : 可用于远程windows主机 (达叔推荐,后面竟然也用到这个软件了) {:&.rollIn}
* teamviewer : 远程软件

[slide]
##开发平时用到的软件
* switchhost : 切换host软件  http://oldj.github.io/SwitchHosts/ {:&.rollIn}
* FileZila : ftp软件
* iTerm : ssh软件
* SecureCRT : ssh软件（需要破解）
* OmniGraffle : 流程图软件
* versions : svn软件（破解后老是崩溃）
* smartSVN svn软件（推荐，需要破解）
* xmind : 思维导图软件
* navicat : mysql管理软件（收费，需要破解）
* sequel pro ：mysql管理软件（推荐）
* beyond compare : 文件对比工具
* foxmail  : 邮件管理软件，收邮件好慢
* Microsoft outlook：（部分内容不显示）
* 自带的邮箱
* tunnelblick 公司vpn软件
* shadowsocks : 翻墙软件 （yizhihongxing.com 每年99）
* rtx
* 钉钉
* charles http抓包工具类似于fiddler
* sourcetree git工具
* macdown markdown编辑器 

[slide]
##常用软件
* thunder 迅雷下载 {:&.rollIn}
* leaf : rss软件
* Alfred : option+空格键打开软件（推荐）+ Workflow
* 微信
* QQ
* chrome
* Mac 上轻量 GIF 录屏小工具 - Kap
* 暴风影音 视频播放器
* 射手影音
* evernote 印象笔记
* 有道云笔记
* noizio 雷雨声模拟器
* keka 压缩
* DaisyDisk 磁盘分析
* homebrew

[slide]
##软件推荐

* window tidy {:&.rollIn}
* tmux
* zsh+iterm

[slide]

##tmux

```javascript
yum install tmux | apt-get install tmux | brew install tmux
touch ~/.tmux.conf
set -g prefix C-a
unbind C-b
bind r source-file ~/.tmux.conf \; display "Reload!"

CTRL+B :
source-file ~/.tmux.conf

tmux new -s session 开启新的tmux
tmux new -s session -d 后台开启新的tmux
tmux ls  列出有哪些窗口
tmux attach -t session 恢复窗口

C-b ? 显示快捷键帮助
C-b C-o 调换窗口位置，类似与vim 里的C-w
C-b 空格键 采用下一个内置布局
C-b ! 把当前窗口变为新窗口
C-b “ 横向分隔窗口
C-b % 纵向分隔窗口
C-b q 显示分隔窗口的编号
C-b o 跳到下一个分隔窗口
C-b 上下键 上一个及下一个分隔窗口
C-b C-方向键 调整分隔窗口大小
C-b c 创建新窗口
C-b 0~9 选择几号窗口
C-b c 创建新窗口
C-b n 选择下一个窗口
C-b l 切换到最后使用的窗口
C-b p 选择前一个窗口
C-b w 以菜单方式显示及选择窗口
C-b t 显示时钟
C-b ; 切换到最后一个使用的面板
C-b x 关闭面板
C-b & 关闭窗口
C-b s 以菜单方式显示和选择会话
C-b d 退出tumx，并保存当前会话，这时，tmux仍在后台运行，可以通过tmux
attach进入 到指定的会话

```

### 快捷键

```javascript
调节音量时，同时按住Shift＋Option，一次可以调节原来的1/4幅度

Command+H——隐藏窗口
Command+M——最小化窗口
Command+N——新建
Command+O——打开
Command+S——保存
Command+shift+S——另存为
Command+W——关闭
Command+Q——退出（小编编最喜欢用的快捷键，关一堆应用不能更爽）
Command＋H：隐藏当前窗口
Command＋C：复制
Command＋V：粘贴
Command＋：保存
Command＋空格：搜索
Command+shift+g 手动输入路径前往某个文件夹

command+空格 spotlight搜索, 无比强大
control+空格 切换输入法
option+空格  Alfred搜索

command+option+esc 强制退出
command+k 调出远程连接
command+delete 删除文件
command+z 撤销
command+shift+3 全屏截图
command+shift+4 区域截图
command+shift+5 窗口截图
command+m 最小化窗口
command+control+电源键 强制重启
浏览图片
command+a全选后按空格键 然后左右滑动触摸板

文件
Command+A——选择全部
Command+I——显示简介
Command+N——新建文件夹
Command+F——搜索
Command+C——复制
Command+V——粘贴（用习惯windows的宝宝，应该能看出来Command就相当于Ctrl~）
Command+delete——删除
Command+shift+delete——清空回收站

control+fn+f2
control+fn+f3

shift+option+commadn+v 纯文本粘贴

option+command+esc 强制退出快捷键

文件对比
vimdiff destfile.txt sourcefile.txt
diff destfile.txt sourcefile.txt
```

##买MAC应该注意什么

* 大内存，大内存，大内存 {:&.rollIn}
* 外设 - 转接线（一转三 HDMI、usb type-c）
* 价钱
* 不要买苹果鼠标。伤手。
* 可以定制