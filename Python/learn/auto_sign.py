#! /usr/bin/python
# -*- coding:utf8 -*-

from selenium import webdriver
from selenium.webdriver.common.action_chains import ActionChains #引入ActionChains鼠标操作类
from selenium.webdriver.common.keys import Keys #引入keys类操作
import time
import os

"""登录"""
def doLogin(browser):
    print '正在执行登录流程....'
    browser.find_element_by_id('username').send_keys(u'17062788')
    browser.find_element_by_id('password').send_keys(u'DionTan')
    browser.find_element_by_xpath(".//button[contains(@class, 'm-b-sm')]").click()
    #browser.find_element_by_name('m-b-sm').click()
    time.sleep(20)

    ## 强制阅读跳过
    while True:
        force = False
        try:
            force = browser.find_element_by_id('tag_completed')
        except Exception as e:
            pass
        if force:
            time.sleep(15)
            force.click()
        else:
            break

    ##返回
    return False if isLoginPage(browser) else True


"""检查当前页面是否为登录"""
def isLoginPage(browser):
    login = False;
    try:
        login = browser.find_element_by_id('login-form')
    except Exception as e:
        pass
    return True if login else False


"""执行签卡"""
def doSignInClick(browser):
    print '正在执行签卡流程.....'
    try:
       browser.find_element_by_id('btn-sign-in').click()
       time.sleep(1)
       browser.find_element_by_id('home-sign-in').click()
    except Exception as e:
       print '签卡失败'
       return False
    return True

"""执行退卡"""
def doSignOutClick(browser):
    print '正在执行签卡流程.....'
    try:
       print browser.find_element_by_id('sign-out-prev').text
       browser.find_element_by_id('sign-out-prev').click()
       time.sleep(1)
       browser.find_element_by_id('sign-out').click()
    except Exception as e:
       print '退卡失败'
       return False
    return True

"""执行"""
def main():
    ## 驱动设置
    iepath = os.path.abspath('D:\WebDriver\IEDriverServer.exe')
    browser = webdriver.Ie(iepath)

    ## 打开页面
    print '正在打开页面....'
    browser.get('http://wos.xxxx.cn/sign/index')
    browser.maximize_window()

    ## 检查登录
    if isLoginPage(browser):
        res = doLogin(browser)
        if res == False:
            print "登录失败！"
            browser.quit()
            quit()
        else:
            print '登录成功...'

    ## 执行打卡
    ##time.sleep(3)
    ##doSignOutClick(browser)
    doSignOutClick(browser)

    ## 关闭
    time.sleep(3)
    browser.quit()

# 入口函数
if __name__ == '__main__':
    main()













