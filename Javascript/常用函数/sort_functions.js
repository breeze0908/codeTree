 //1.返回两个指定数值之间的随机数（不包含最大值）

 // a. 获取两个指定数值之间随机浮点数。
 function getRandomFloat(min, max) {
   return Math.random() * (max - min) + min;
 }


 //b、获取两个指定数值之间随机整数。
 function getRandomInt(min, max) {
   return Math.floor(Math.random() * (max - min)) + min;
 }


 //2.返回两个指定数值之间的随机数（包含最大值）
 function getRandomInRange(min, max) {
   return Math.floor(Math.random() * (max - min + 1)) + min;
 }


 //3.抛硬币（随机布尔值）
 function coinToss() {
   return Math.floor(Math.random() * 2);
 }


 //从指定的整数数组中获取随机数
 var numPool = [1, 3, 5, 7, 9, 10],
   rand = numPool[Math.floor(Math.random() * numPool.length)];


 //实现洗牌功能
 //这里的洗牌功能是指有一个整数数组，里面填充有一些整数，然后随机的将整数打乱，并将打乱后的整数放入一个新的数组，然后一次性的将它们输出出来。
 var numPool = [13, 21, 36, 14, 27, 10];

 function shuffle(numPool) {
   for (var j, x, i = numPool.length; i; j = parseInt(Math.random() * i), x = numPool[--i], numPool[i] = numPool[j], numPool[j] = x);
   return numPool;
 };


 //使用不重复的随机整数来填充数组
 var numReserve = []
 while (numReserve.length < 12) {
   var randomNumber = Math.ceil(Math.random() * 1000);
   var found = false;
   for (var i = 0; i < numReserve.length; i++) {
     if (numReserve[i] === randomNumber) {
       found = true;
       break;
     }
   }
   if (!found) {
     numReserve[numReserve.length] = randomNumber;
   }
 }


 //使用Web Cryptography API来生成一组随机数
 Web Cryptography API是W3C发布的Web加密API（ Web Cryptography API） 的标准草案。 该文档定义了在Web应用中执行基本加解密操作的JavaScript API， 如哈希操作（ hash）、 签名生成和验证（ signature generation and verification）， 以及加密解密等。 此外， 该文档还描述了与密钥管理有关的操作。 API的用途覆盖用户或服务的认证、 文档或代码的签名、 通信的机密性与完整性保证等。

 var cryptoStor = new Uint16Array(8);

 上面的代码会生成包含8个16位无符号整数的数组。 其它可以使用整数选项有： Int8Array， Uint8Array， int16Array， Int32Array 和 Uint32Array。
 然后使用随机数来填充数组。
 window.crypto.getRandomValues(cryptoStor);