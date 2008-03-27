统一处理引号
DB::quote 参数支持：字符串、数组


统一处理数据库字段
DB::quoteInto
参数支持：
quoteInto('field = ?','value');  // 单一占位符
quoteInto('field = :field and field1 = :field1',array('field'=>'value','field1'=>'value1'));  // 多个占位符

统一处理字段冲突
DB::quoteIdentifier
quoteIdentifier('field'); // 单一字符串
quoteIdentifier('field,field1,field2'); // 逗号分割字符串
quoteIdentifier(array('field','field1','field2')); // 数组
quoteIdentifier('table.field AS field1'); // 前缀表 和 AS 字段
