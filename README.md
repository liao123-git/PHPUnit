# PHPUnit
### [官方文档](https://phpunit.readthedocs.io/zh_CN/latest/index.html 'PHPUnit')

### windows 下使用
- 首先到 [PHPUnit 官网](https://phpunit.de 'PHPUnit') 下载相应的版本。得到 .phar 文件，并把名字改为 phpunit.phar 。
- 把该文件放到任意位置。以我自己为例，我把它放到我们项目的目录下。
- 右键 我的电脑 ，选择 属性 。点击左侧的 高级系统设置 。此时弹出 系统属性 ，选择 高级 选项卡，点击右下角的 环境变量 。 在用户变量里面，双击 PATH ，在 变量值 后面添加 项目路径 。配置这个是为了在任意位置都能使用PHPUnit，不配置的话，需要在phpunit.phar的路径下才能使用PHPUnit
    - 高版本貌似无效 
- 按快捷键 Win + R ，输入cmd并回车。进入存放 phpunit.phar 的路径。输入第一行回车，再输入第二行。如果得到输出第三行则表示配置好了。
    - > echo @php "%~dp0phpunit.phar" %* > phpunit.cmd 
    - > phpunit --version 
    - > PHPUnit x.y.z by Sebastian Bergmann and contributors
- 也可以用 Composer 安装
    - > composer require --dev phpunit/phpunit ^latest
    - > echo @php "%~dp0vendor/bin/phpunit" %*
    
### 编写 PHPUnit 测试
- 针对类 Class 的测试写在类 ClassTest 中
- ClassTest（通常）继承自 PHPUnit\Framework\TestCase
- 测试都是命名为 test* 的公用方法。
    - 也可以在方法的文档注释块（docblock）中使用 @test 标注将其标记为测试方法。
    
### 测试的依赖关系
- 单元测试主要是作为一种良好实践来编写的，它能帮助开发人员识别并修复 bug、重构代码，还可以看作被测软件单元的文档。要实现这些好处，理想的单元测试应当覆盖程序中所有可能的路径。一个单元测试通常覆盖一个函数或方法中的一个特定路径。但是，测试方法不一定是封装良好的独立实体。测试方法之间经常有隐含的依赖关系暗藏在测试的实现方案中。
- PHPUnit支持对测试方法之间的显式依赖关系进行声明。这种依赖关系并不是定义在测试方法的执行顺序中，而是允许生产者（producer）返回一个测试基境（fixture）的实例，并将此实例传递给依赖于它的消费者（consumer）们。
  - 生产者（producer），是能生成被测单元并将其作为返回值的测试方法。
  - 消费者（consumer），是依赖于一个或多个生产者及其返回值的测试方法。
  - 例如 JS Promise 的链式调用
  - 写在消费者前的注释里
    - >@depends testXXX
- 默认情况下，生产者所产生的返回值将“原样”传递给相应的消费者。这意味着，如果生产者返回的是一个对象，那么传递给消费者的将是指向此对象的引用。但同样也可以（a）通过 @depends clone 来传递指向（深）拷贝对象的引用，或（b）通过 @depends shallowClone 来传递指向（正常浅）克隆对象（基于 PHP 关键字 clone）的引用。
- 测试可以使用多个 @depends 标注。PHPUnit 不会更改测试的运行顺序，因此你需要自行保证某个测试所依赖的所有测试均出现于这个测试之前。
- 拥有多个 @depends 标注的测试，其第一个参数是第一个生产者提供的基境，第二个参数是第二个生产者提供的基境，以此类推。

### 数据供给器
- 测试方法可以接受任意参数。这些参数由一个或多个数据供给器方法提供。用 @dataProvider 标注来指定要使用的数据供给器方法。
    - >@dataProvider testXXX
- 数据供给器方法必须声明为 public，其返回值要么是一个数组，其每个元素也是数组；要么是一个实现了 Iterator 接口的对象，在对它进行迭代时每步产生一个数组。每个数组都是测试数据集的一部分，将以它的内容作为参数来调用测试方法。
- 当使用到大量数据集时，最好逐个用字符串键名对其命名，避免用默认的数字键名。这样输出信息会更加详细些，其中将包含打断测试的数据集所对应的名称。
- Iterator 迭代器接口
    - PHP Iterator接口的作用是允许对象以自己的方式迭代内部的数据，从而使它可以被循环访问
    - [官方文档](https://www.php.net/manual/zh/class.iterator.php 'Iterator')
- 如果测试同时从 @dataProvider 方法和一个或多个 @depends 测试接收数据，那么来自于数据供给器的参数将先于来自所依赖的测试的。来自于所依赖的测试的参数对于每个数据集都是一样的。
- 如果一个测试依赖于另外一个使用了数据供给器的测试，仅当被依赖的测试至少能在一组数据上成功时，依赖于它的测试才会运行。使用了数据供给器的测试，其运行结果是无法注入到依赖于此测试的其他测试中的。
- 所有数据供给器方法的执行都是在对 setUpBeforeClass() 静态方法的调用和第一次对 setUp() 方法的调用之前完成的。因此，无法在数据供给器中使用创建于这两个方法内的变量。这是必须的，这样 PHPUnit 才能计算测试的总数量。

### 对异常进行测试
- >@expectException 
    - 标注来测试被测代码中是否抛出了异常。
- 除了 expectException() 方法外，还有 expectExceptionCode()、expectExceptionMessage() 和 expectExceptionMessageMatches() 方法可以用于为被测代码所抛出的异常建立预期。
- 用于 try / catch 抛出报错

### 对 PHP 错误、警告和通知进行测试
- 如果测试代码使用了会触发错误的 PHP 内建函数，比如 fopen，有时候在测试中使用错误抑制符会很有用。通过抑制住错误通知，就能对返回值进行检查，否则错误通知将会导致 PHPUnit 的错误处理程序抛出异常。
    - > @
    
### 对输出进行测试
- 有时候，想要断言（比如说）某方法的运行过程中生成了预期的输出（例如，通过 echo 或 print）。PHPUnit\Framework\TestCase 类使用 PHP 的输出缓冲特性来为此提供必要的功能支持。
- > expectOutputString()
    - [更多方法](https://phpunit.readthedocs.io/zh_CN/latest/writing-tests-for-phpunit.html#writing-tests-for-phpunit-output-tables-api 'more')
- 如果没有产生预期的输出，测试将计为失败。

### 错误相关信息的输出
- 当有测试失败时，PHPUnit 全力提供尽可能多的有助于找出问题所在的上下文信息。
- 当生成的输出很长而难以阅读时，PHPUnit 将对其进行分割，并在每个差异附近提供少数几行上下文信息。
- 边缘情况
 - 当比较失败时，PHPUnit 为输入值建立文本表示，然后以此进行对比。这种实现导致在差异指示中显示出来的问题可能比实际上存在的多。
 - 这种情况只出现在对数组或者对象使用 assertEquals() 或其他“弱”比较函数时。
 - assertEquals 函数不会比对字符串和数值，但是输出的信息会显示字符串和数值的错误。
 
### 基境
- 在编写测试时，最费时的部分之一是编写代码来将整个场景设置成某个已知的状态，并在测试结束后将其复原到初始状态。这个已知的状态称为测试的基境（fixture）。
- PHPUnit 支持共享建立基境的代码。在运行某个测试方法前，会调用一个名叫 setUp() 的模板方法。setUp() 是创建测试所用对象的地方。当测试方法运行结束后，不管是成功还是失败，都会调用另外一个名叫 tearDown() 的模板方法。tearDown() 是清理测试所用对象的地方。
- 测试类的每个测试方法都会运行一次 setUp() 和 tearDown() 模板方法（同时，每个测试方法都是在一个全新的测试类实例上运行的）。
- 如果拥有两个测试，它们的基境建立工作略有不同，该怎么办？有两种可能：
  - 如果两个 setUp() 代码仅有微小差异，把有差异的代码内容从 setUp() 移到测试方法内。
  - 如果两个 setUp() 是确实不一样，那么需要另外一个测试用例类。参考基境建立工作的不同之处来命名这个类。
- 基境共享
    - 有几个好的理由来在测试之间共享基境，但是大部分情况下，在测试之间共享基境的需求都源于某个未解决的设计问题。
    - 一个有实际意义的多测试间共享基境的例子是数据库连接：只登录数据库一次，然后重用此连接，而不是每个测试都建立一个新的数据库连接。这样能加快测试的运行。
- 全局状态
    - 使用全局变量的代码也一样。通常情况下，欲测代码和全局变量之间会强烈耦合，并且其创建无法控制。另外一个问题是，一个测试对全局变量的改变可能会破坏另外一个测试。
        - > $GLOBALS['xxx']
        - > global $foo 
    - 除了全局变量，类的静态属性也是一种全局状态。
    
### 有风险的测试
- 无用测试
    - 默认情况下，PHPUnit 会更严格地对待事实上不测试任何内容的测试。此项检查可以用命令行选项  或在 PHPUnit 的配置文件中设置  来禁用。
        - > --dont-report-useless-tests
            - 命令行
        - > beStrictAboutTestsThatDoNotTestAnything="false"
            - PHPUnit 的配置文件
- 测试执行期间产生的输出
    - PHPUnit 可以更严格对待测试执行期间产生的输出。 此项检查可以用命令行选项  或在 PHPUnit 的配置文件中设置  来启用。
        - > --disallow-test-output
            - 命令行
        - >  beStrictAboutOutputDuringTests="true"
            - PHPUnit 的配置文件
- 测试执行时长的超时限制
    - windows 下无法安装 pcntl 扩展，所以用不了
    - 如果安装了 PHP_Invoker 包并且 pcntl 扩展可用，那么可以对测试的执行时长进行限制。此时间限制可以用命令行选项  或在 PHPUnit 的配置文件中设置  来启用。
        - > --enforce-time-limit
            - 命令行
        - >  enforceTimeLimit="true"
            - PHPUnit 的配置文件
    - > @large
        - 带有 @large 标注的测试如果执行时间超过 60 秒将视为失败。此超时限制可以通过配置文件中的 timeoutForLargeTests 属性进行配置。
    - > @medium 
        - 带有 @medium 标注的测试如果执行时间超过 60 秒将视为失败。此超时限制可以通过配置文件中的 timeoutForLargeTests 属性进行配置。
    - > @small 
        - 带有 @small 标注的测试如果执行时间超过 60 秒将视为失败。此超时限制可以通过配置文件中的 timeoutForLargeTests 属性进行配置。

### 组织测试
- 用文件系统来编排测试套件
    - 编排测试套件的各种方式中，最简单的大概就是把所有测试用例源文件放在一个测试目录中。通过对测试目录进行递归遍历，PHPUnit 能自动发现并运行测试。
    - 在这个项目的目录结构中，可以看到 tests 目录下的测试用例类镜像了 src 目录下被测系统（SUT，System Under Test）的包（package）与类（class）的结构。
    - 要运行这个库的全部测试，将 PHPUnit 命令行测试执行器指向测试目录：
        - > phpunit --bootstrap ./app/src/autoload.php ./app/tests
        - 当 PHPUnit 命令行测试执行器指向一个目录时，它会在目录下查找 *Test.php 文件。
    - 如果只想运行在 UserTest 文件中的 tests/UserTest.php 测试用例类中声明的测试，可以使用如下命令：
        - > phpunit --bootstrap ./app/src/autoload.php ./app/tests/UserTest.php
    - 这种方法的缺点是无法控制测试的运行顺序。
- 用 XML 配置来编排测试套件
    - XML 文件内的 <testsuite> 标签的 name 属性是你想要被测试的文件
    - > phpunit --bootstrap xxx --testsuite xxx
        - 要运行测试套件，用 --testsuite
        - 第一个 autoload.php 文件路径
        - 第二个 XML 文件内的 <testsuite> 标签的 name 属性

### XML 配置文件
- > phpunit
    - > testsuite
        - 必须拥有 name 属性，可以有一个或多个 <directory> 及 <file> 子元素，分别代表需要搜索测试的目录及元素。 
    - > php
        - 元素及其子元素用于配置 PHP 设置、常量以及全局变量。同时也可用于向 include_path 前面添加内容。
        - > ini
            - 此元素可用于设置 PHP 配置。
        - > const
            - 此元素可用于设置全局常数。


### PHP 基础
- [PHP 官网](https://www.php.net 'PHP')
- 开启 PHP 严格模式 
    - > declare(strict_types=1);
    - 规定函数返回值类型 void , int 等等
    - phpstorm 有可能会划红线，是因为编辑器对应的 PHP 版本没有到7。
        - 到设置里搜索 Languages ，在左侧点击 PHP ，再选择对应的版本就行了。
- > fopen()
    - 打开文件或者 URL
    - > fopen ( string $filename , string $mode , bool $use_include_path = false , resource $context = ? ) : resource
    - fopen() 将 filename 指定的名字资源绑定到一个流上。
    - mode 参数指定了所要求到该流的访问类型。
        - 比如 'r' 只读方式打开，将文件指针指向文件头。
- > func_get_args()
    - > func_get_args ( ) : array 
    - 获取函数参数列表的数组。
- > rewind()
    - > rewind ( resource $handle ) : bool
    - 倒回文件指针的位置
    - 将 handle 的文件位置指针设为文件流的开头。
- > fgetcsv()
    - 从文件指针中读入一行并解析 CSV 字段
    - > fgetcsv ( resource $handle , int $length = 0 , string $delimiter = ',' , string $enclosure = '"' , string $escape = '\\' ) : array
    - 执行完指针应该是会到下一行开头的。
- > __ METHOD __
    - 返回类名称与函数的名称
    
### 一些小坑
- PHPUnit 官方文档中 ['示例 2.8 CsvFileIterator 类'](https://phpunit.readthedocs.io/zh_CN/latest/writing-tests-for-phpunit.html#writing-tests-for-phpunit-data-providers 'PHPUnit')
，从文件指针中读入一行并解析 CSV 字段 -- 'fgetcsv' 函数获取到的数组内的元素皆为字符串，如果需要数值类型需手动转换。
