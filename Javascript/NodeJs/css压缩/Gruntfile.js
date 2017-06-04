module.exports = function(grunt) {
  // 项目配置信息
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),   // grunt会读取package.json中的文件信息

    concat : {      // concat插件的配置信息
       test_grunt : {   // 名称而已~
         files : {  // 将source目录下doT.js和common.js合并成tmp.js，并保存到dist目录下
           'dist/main.js': ['new_js/domReady.js','new_js/dom.js','new_js/eventutil.js','new_js/switchtab_focus.js','new_js/lazy.js','new_js/main.js']
         }
       }
     },
     uglify: {  // uglify插件的配置信息
       test_grunt : {   // 名称而已~
          options: {
            sourceMap: false // 允许自动生成source map文件
          },
         files : {  // 将tmp.js压缩成tmp.min.js
           'dist/main.min.js' : 'dist/main.js'
         }
       },
       footer:{
          files : { 
               'dist/footer.js': ['new_js/footer.js']
             }
      },
      zzyg:{
          files : { 
               'dist/zzyg.js': ['new_js/zzyg.js']
             }
      }
     }

    
  });
  

  // 加载插件
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');

  // 执行任务，任务名称是default
  grunt.registerTask('default', ['concat','uglify']);

}