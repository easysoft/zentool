# z tool
Z持续集成工具

## Features
TODO:

## QuickStart
1. 执行以下命令，将禅道信息导入到可执行文件中；
```
echo '{"Url":"http://127.0.0.1:20080","Account":"admin","Password":"P2ssw0rd2"}' >> z.exe
```
2. 如果需要，克隆开发分支所在的代码到指定目录；
```
git clone http://192.168.1.161:51080/root/ci_test_testng.git ci_test_testng_branch
cd ci_test_testng_branch
git checkout ci_branch
```
3. 进入或使用-s指定被合并分支所在的目录，执行命令；
```
z.exe mr -s /Users/aaron/ci_test_testng_branch -d master --verbose
```
4. 工具会签出目标分支（这里是主分支master）的代码，进行预合并；
5. 工具向禅道发送请求，传入代码库地址repoUrl参数，获取以下文件服务器配置；
```
data:
    fileServerUrl      string
    fileServerAccount  string
    fileServerPassword string
```
6. 工具向文件服务器上传合并后的代码zip文件；
7. 工具使用以下请求参数，向禅道发送合并信息；
```
data:
    mergeResult bool
    mergeMsg    string
    diffMsg     string
    uploadMsg   string
```
8. 禅道返回响应结果，包含以下字段信息；
```
gitLabMRId int
ciBuildId  int
```

## Licenses
All source code is licensed under the [GPLv3 License](LICENSE.md).
