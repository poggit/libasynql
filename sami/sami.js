
window.projectVersion = 'master';

(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:poggit" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="poggit.html">poggit</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:poggit_libasynql" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="poggit/libasynql.html">libasynql</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:poggit_libasynql_base" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="poggit/libasynql/base.html">base</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:poggit_libasynql_base_BaseSqlThread" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/base/BaseSqlThread.html">BaseSqlThread</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_base_DatabaseConnectionImpl" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/base/DatabaseConnectionImpl.html">DatabaseConnectionImpl</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_base_SqlThreadPool" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/base/SqlThreadPool.html">SqlThreadPool</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:poggit_libasynql_generic" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="poggit/libasynql/generic.html">generic</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:poggit_libasynql_generic_GenericStatementFileParseException" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/generic/GenericStatementFileParseException.html">GenericStatementFileParseException</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_generic_GenericStatementFileParser" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/generic/GenericStatementFileParser.html">GenericStatementFileParser</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_generic_GenericStatementImpl" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/generic/GenericStatementImpl.html">GenericStatementImpl</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_generic_GenericVariable" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/generic/GenericVariable.html">GenericVariable</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_generic_MysqlStatementImpl" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/generic/MysqlStatementImpl.html">MysqlStatementImpl</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_generic_SqliteStatementImpl" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/generic/SqliteStatementImpl.html">SqliteStatementImpl</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:poggit_libasynql_mysql" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="poggit/libasynql/mysql.html">mysql</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:poggit_libasynql_mysql_MysqlColumnInfo" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/mysql/MysqlColumnInfo.html">MysqlColumnInfo</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_mysql_MysqlCredentials" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/mysql/MysqlCredentials.html">MysqlCredentials</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_mysql_MysqlFlags" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/mysql/MysqlFlags.html">MysqlFlags</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_mysql_MysqlThread" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/mysql/MysqlThread.html">MysqlThread</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_mysql_MysqlTypes" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/mysql/MysqlTypes.html">MysqlTypes</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:poggit_libasynql_result" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="poggit/libasynql/result.html">result</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:poggit_libasynql_result_SqlChangeResult" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/result/SqlChangeResult.html">SqlChangeResult</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_result_SqlColumnInfo" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/result/SqlColumnInfo.html">SqlColumnInfo</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_result_SqlInsertResult" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/result/SqlInsertResult.html">SqlInsertResult</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_result_SqlSelectResult" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="poggit/libasynql/result/SqlSelectResult.html">SqlSelectResult</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:poggit_libasynql_DatabaseConnection" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="poggit/libasynql/DatabaseConnection.html">DatabaseConnection</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_GenericStatement" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="poggit/libasynql/GenericStatement.html">GenericStatement</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_SqlDialect" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="poggit/libasynql/SqlDialect.html">SqlDialect</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_SqlError" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="poggit/libasynql/SqlError.html">SqlError</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_SqlResult" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="poggit/libasynql/SqlResult.html">SqlResult</a>                    </div>                </li>                            <li data-name="class:poggit_libasynql_SqlThread" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="poggit/libasynql/SqlThread.html">SqlThread</a>                    </div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": "poggit.html", "name": "poggit", "doc": "Namespace poggit"},{"type": "Namespace", "link": "poggit/libasynql.html", "name": "poggit\\libasynql", "doc": "Namespace poggit\\libasynql"},{"type": "Namespace", "link": "poggit/libasynql/base.html", "name": "poggit\\libasynql\\base", "doc": "Namespace poggit\\libasynql\\base"},{"type": "Namespace", "link": "poggit/libasynql/generic.html", "name": "poggit\\libasynql\\generic", "doc": "Namespace poggit\\libasynql\\generic"},{"type": "Namespace", "link": "poggit/libasynql/mysql.html", "name": "poggit\\libasynql\\mysql", "doc": "Namespace poggit\\libasynql\\mysql"},{"type": "Namespace", "link": "poggit/libasynql/result.html", "name": "poggit\\libasynql\\result", "doc": "Namespace poggit\\libasynql\\result"},
            {"type": "Interface", "fromName": "poggit\\libasynql", "fromLink": "poggit/libasynql.html", "link": "poggit/libasynql/DatabaseConnection.html", "name": "poggit\\libasynql\\DatabaseConnection", "doc": "&quot;Represents a database connection or a group of database connections&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_loadQueryFile", "name": "poggit\\libasynql\\DatabaseConnection::loadQueryFile", "doc": "&quot;Loads pre-formatted queries from a readable stream resource.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_loadQuery", "name": "poggit\\libasynql\\DatabaseConnection::loadQuery", "doc": "&quot;Loads a pre-formatted query.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_executeGeneric", "name": "poggit\\libasynql\\DatabaseConnection::executeGeneric", "doc": "&quot;Executes a generic query that either succeeds or fails.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_executeChange", "name": "poggit\\libasynql\\DatabaseConnection::executeChange", "doc": "&quot;Executes a query that changes data.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_executeInsert", "name": "poggit\\libasynql\\DatabaseConnection::executeInsert", "doc": "&quot;Executes an insert query that results in an insert ID.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_executeSelect", "name": "poggit\\libasynql\\DatabaseConnection::executeSelect", "doc": "&quot;Executes a select query that returns an SQL result set. This does not strictly need to be SELECT queries -- reflection queries like MySQL&#039;s &lt;code&gt;SHOW TABLES&lt;\/code&gt; query are also allowed.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_close", "name": "poggit\\libasynql\\DatabaseConnection::close", "doc": "&quot;Closes the connection and\/or all child connections. Remember to call this method when the plugin is disabled or the data provider is switched.&quot;"},
            
            {"type": "Interface", "fromName": "poggit\\libasynql", "fromLink": "poggit/libasynql.html", "link": "poggit/libasynql/GenericStatement.html", "name": "poggit\\libasynql\\GenericStatement", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\GenericStatement", "fromLink": "poggit/libasynql/GenericStatement.html", "link": "poggit/libasynql/GenericStatement.html#method_getDialect", "name": "poggit\\libasynql\\GenericStatement::getDialect", "doc": "&quot;Returns the dialect this query is intended for.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\GenericStatement", "fromLink": "poggit/libasynql/GenericStatement.html", "link": "poggit/libasynql/GenericStatement.html#method_getName", "name": "poggit\\libasynql\\GenericStatement::getName", "doc": "&quot;Returns the identifier name of this query&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\GenericStatement", "fromLink": "poggit/libasynql/GenericStatement.html", "link": "poggit/libasynql/GenericStatement.html#method_getVariables", "name": "poggit\\libasynql\\GenericStatement::getVariables", "doc": "&quot;Returns the variables required by this statement&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\GenericStatement", "fromLink": "poggit/libasynql/GenericStatement.html", "link": "poggit/libasynql/GenericStatement.html#method_format", "name": "poggit\\libasynql\\GenericStatement::format", "doc": "&quot;Creates a query based on the args and the backend&quot;"},
            
            {"type": "Interface", "fromName": "poggit\\libasynql", "fromLink": "poggit/libasynql.html", "link": "poggit/libasynql/SqlDialect.html", "name": "poggit\\libasynql\\SqlDialect", "doc": "&quot;&quot;"},
                    
            {"type": "Interface", "fromName": "poggit\\libasynql", "fromLink": "poggit/libasynql.html", "link": "poggit/libasynql/SqlThread.html", "name": "poggit\\libasynql\\SqlThread", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_join", "name": "poggit\\libasynql\\SqlThread::join", "doc": "&quot;Joins the thread&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_stopRunning", "name": "poggit\\libasynql\\SqlThread::stopRunning", "doc": "&quot;Signals the thread to stop waiting for queries when the send buffer is cleared.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_addQuery", "name": "poggit\\libasynql\\SqlThread::addQuery", "doc": "&quot;Adds a query to the queue.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_readResults", "name": "poggit\\libasynql\\SqlThread::readResults", "doc": "&quot;Handles the results that this query has completed&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_connCreated", "name": "poggit\\libasynql\\SqlThread::connCreated", "doc": "&quot;Checks if the initial connection has been made, no matter successful or not.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_hasConnError", "name": "poggit\\libasynql\\SqlThread::hasConnError", "doc": "&quot;Checks if the initial connection failed.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_getConnError", "name": "poggit\\libasynql\\SqlThread::getConnError", "doc": "&quot;Gets the error of the initial connection.&quot;"},
            
            {"type": "Interface", "fromName": "poggit\\libasynql\\mysql", "fromLink": "poggit/libasynql/mysql.html", "link": "poggit/libasynql/mysql/MysqlFlags.html", "name": "poggit\\libasynql\\mysql\\MysqlFlags", "doc": "&quot;Result field flags returned by MySQL&quot;"},
                    
            {"type": "Interface", "fromName": "poggit\\libasynql\\mysql", "fromLink": "poggit/libasynql/mysql.html", "link": "poggit/libasynql/mysql/MysqlTypes.html", "name": "poggit\\libasynql\\mysql\\MysqlTypes", "doc": "&quot;Result field types returned by MySQL&quot;"},
                    
            
            {"type": "Class", "fromName": "poggit\\libasynql", "fromLink": "poggit/libasynql.html", "link": "poggit/libasynql/DatabaseConnection.html", "name": "poggit\\libasynql\\DatabaseConnection", "doc": "&quot;Represents a database connection or a group of database connections&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_loadQueryFile", "name": "poggit\\libasynql\\DatabaseConnection::loadQueryFile", "doc": "&quot;Loads pre-formatted queries from a readable stream resource.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_loadQuery", "name": "poggit\\libasynql\\DatabaseConnection::loadQuery", "doc": "&quot;Loads a pre-formatted query.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_executeGeneric", "name": "poggit\\libasynql\\DatabaseConnection::executeGeneric", "doc": "&quot;Executes a generic query that either succeeds or fails.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_executeChange", "name": "poggit\\libasynql\\DatabaseConnection::executeChange", "doc": "&quot;Executes a query that changes data.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_executeInsert", "name": "poggit\\libasynql\\DatabaseConnection::executeInsert", "doc": "&quot;Executes an insert query that results in an insert ID.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_executeSelect", "name": "poggit\\libasynql\\DatabaseConnection::executeSelect", "doc": "&quot;Executes a select query that returns an SQL result set. This does not strictly need to be SELECT queries -- reflection queries like MySQL&#039;s &lt;code&gt;SHOW TABLES&lt;\/code&gt; query are also allowed.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\DatabaseConnection", "fromLink": "poggit/libasynql/DatabaseConnection.html", "link": "poggit/libasynql/DatabaseConnection.html#method_close", "name": "poggit\\libasynql\\DatabaseConnection::close", "doc": "&quot;Closes the connection and\/or all child connections. Remember to call this method when the plugin is disabled or the data provider is switched.&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql", "fromLink": "poggit/libasynql.html", "link": "poggit/libasynql/GenericStatement.html", "name": "poggit\\libasynql\\GenericStatement", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\GenericStatement", "fromLink": "poggit/libasynql/GenericStatement.html", "link": "poggit/libasynql/GenericStatement.html#method_getDialect", "name": "poggit\\libasynql\\GenericStatement::getDialect", "doc": "&quot;Returns the dialect this query is intended for.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\GenericStatement", "fromLink": "poggit/libasynql/GenericStatement.html", "link": "poggit/libasynql/GenericStatement.html#method_getName", "name": "poggit\\libasynql\\GenericStatement::getName", "doc": "&quot;Returns the identifier name of this query&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\GenericStatement", "fromLink": "poggit/libasynql/GenericStatement.html", "link": "poggit/libasynql/GenericStatement.html#method_getVariables", "name": "poggit\\libasynql\\GenericStatement::getVariables", "doc": "&quot;Returns the variables required by this statement&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\GenericStatement", "fromLink": "poggit/libasynql/GenericStatement.html", "link": "poggit/libasynql/GenericStatement.html#method_format", "name": "poggit\\libasynql\\GenericStatement::format", "doc": "&quot;Creates a query based on the args and the backend&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql", "fromLink": "poggit/libasynql.html", "link": "poggit/libasynql/SqlDialect.html", "name": "poggit\\libasynql\\SqlDialect", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "poggit\\libasynql", "fromLink": "poggit/libasynql.html", "link": "poggit/libasynql/SqlError.html", "name": "poggit\\libasynql\\SqlError", "doc": "&quot;Represents a generic error when executing a SQL statement.&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\SqlError", "fromLink": "poggit/libasynql/SqlError.html", "link": "poggit/libasynql/SqlError.html#method___construct", "name": "poggit\\libasynql\\SqlError::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlError", "fromLink": "poggit/libasynql/SqlError.html", "link": "poggit/libasynql/SqlError.html#method_getStage", "name": "poggit\\libasynql\\SqlError::getStage", "doc": "&quot;Returns the stage of query execution at which the error occurred.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlError", "fromLink": "poggit/libasynql/SqlError.html", "link": "poggit/libasynql/SqlError.html#method_getErrorMessage", "name": "poggit\\libasynql\\SqlError::getErrorMessage", "doc": "&quot;Returns the error message&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlError", "fromLink": "poggit/libasynql/SqlError.html", "link": "poggit/libasynql/SqlError.html#method_getQuery", "name": "poggit\\libasynql\\SqlError::getQuery", "doc": "&quot;Returns the original query&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlError", "fromLink": "poggit/libasynql/SqlError.html", "link": "poggit/libasynql/SqlError.html#method_getArgs", "name": "poggit\\libasynql\\SqlError::getArgs", "doc": "&quot;Returns the original arguments passed to the query&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql", "fromLink": "poggit/libasynql.html", "link": "poggit/libasynql/SqlResult.html", "name": "poggit\\libasynql\\SqlResult", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "poggit\\libasynql", "fromLink": "poggit/libasynql.html", "link": "poggit/libasynql/SqlThread.html", "name": "poggit\\libasynql\\SqlThread", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_join", "name": "poggit\\libasynql\\SqlThread::join", "doc": "&quot;Joins the thread&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_stopRunning", "name": "poggit\\libasynql\\SqlThread::stopRunning", "doc": "&quot;Signals the thread to stop waiting for queries when the send buffer is cleared.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_addQuery", "name": "poggit\\libasynql\\SqlThread::addQuery", "doc": "&quot;Adds a query to the queue.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_readResults", "name": "poggit\\libasynql\\SqlThread::readResults", "doc": "&quot;Handles the results that this query has completed&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_connCreated", "name": "poggit\\libasynql\\SqlThread::connCreated", "doc": "&quot;Checks if the initial connection has been made, no matter successful or not.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_hasConnError", "name": "poggit\\libasynql\\SqlThread::hasConnError", "doc": "&quot;Checks if the initial connection failed.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\SqlThread", "fromLink": "poggit/libasynql/SqlThread.html", "link": "poggit/libasynql/SqlThread.html#method_getConnError", "name": "poggit\\libasynql\\SqlThread::getConnError", "doc": "&quot;Gets the error of the initial connection.&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\base", "fromLink": "poggit/libasynql/base.html", "link": "poggit/libasynql/base/BaseSqlThread.html", "name": "poggit\\libasynql\\base\\BaseSqlThread", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method___construct", "name": "poggit\\libasynql\\base\\BaseSqlThread::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_run", "name": "poggit\\libasynql\\base\\BaseSqlThread::run", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_isWorking", "name": "poggit\\libasynql\\base\\BaseSqlThread::isWorking", "doc": "&quot;Returns true if this thread is working, false if waiting for requests&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_stopRunning", "name": "poggit\\libasynql\\base\\BaseSqlThread::stopRunning", "doc": "&quot;Signals the thread to stop waiting for queries when the send buffer is cleared.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_addQuery", "name": "poggit\\libasynql\\base\\BaseSqlThread::addQuery", "doc": "&quot;Adds a query to the queue.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_readResults", "name": "poggit\\libasynql\\base\\BaseSqlThread::readResults", "doc": "&quot;Handles the results that this query has completed&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_connCreated", "name": "poggit\\libasynql\\base\\BaseSqlThread::connCreated", "doc": "&quot;Checks if the initial connection has been made, no matter successful or not.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_hasConnError", "name": "poggit\\libasynql\\base\\BaseSqlThread::hasConnError", "doc": "&quot;Checks if the initial connection failed.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_getConnError", "name": "poggit\\libasynql\\base\\BaseSqlThread::getConnError", "doc": "&quot;Gets the error of the initial connection.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_createConn", "name": "poggit\\libasynql\\base\\BaseSqlThread::createConn", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_executeQuery", "name": "poggit\\libasynql\\base\\BaseSqlThread::executeQuery", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\BaseSqlThread", "fromLink": "poggit/libasynql/base/BaseSqlThread.html", "link": "poggit/libasynql/base/BaseSqlThread.html#method_close", "name": "poggit\\libasynql\\base\\BaseSqlThread::close", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\base", "fromLink": "poggit/libasynql/base.html", "link": "poggit/libasynql/base/DatabaseConnectionImpl.html", "name": "poggit\\libasynql\\base\\DatabaseConnectionImpl", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\base\\DatabaseConnectionImpl", "fromLink": "poggit/libasynql/base/DatabaseConnectionImpl.html", "link": "poggit/libasynql/base/DatabaseConnectionImpl.html#method___construct", "name": "poggit\\libasynql\\base\\DatabaseConnectionImpl::__construct", "doc": "&quot;Creates a DatabaseConnection.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\DatabaseConnectionImpl", "fromLink": "poggit/libasynql/base/DatabaseConnectionImpl.html", "link": "poggit/libasynql/base/DatabaseConnectionImpl.html#method_loadQueryFile", "name": "poggit\\libasynql\\base\\DatabaseConnectionImpl::loadQueryFile", "doc": "&quot;Loads pre-formatted queries from a readable stream resource.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\DatabaseConnectionImpl", "fromLink": "poggit/libasynql/base/DatabaseConnectionImpl.html", "link": "poggit/libasynql/base/DatabaseConnectionImpl.html#method_loadQuery", "name": "poggit\\libasynql\\base\\DatabaseConnectionImpl::loadQuery", "doc": "&quot;Loads a pre-formatted query.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\DatabaseConnectionImpl", "fromLink": "poggit/libasynql/base/DatabaseConnectionImpl.html", "link": "poggit/libasynql/base/DatabaseConnectionImpl.html#method_executeGeneric", "name": "poggit\\libasynql\\base\\DatabaseConnectionImpl::executeGeneric", "doc": "&quot;Executes a generic query that either succeeds or fails.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\DatabaseConnectionImpl", "fromLink": "poggit/libasynql/base/DatabaseConnectionImpl.html", "link": "poggit/libasynql/base/DatabaseConnectionImpl.html#method_executeChange", "name": "poggit\\libasynql\\base\\DatabaseConnectionImpl::executeChange", "doc": "&quot;Executes a query that changes data.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\DatabaseConnectionImpl", "fromLink": "poggit/libasynql/base/DatabaseConnectionImpl.html", "link": "poggit/libasynql/base/DatabaseConnectionImpl.html#method_executeInsert", "name": "poggit\\libasynql\\base\\DatabaseConnectionImpl::executeInsert", "doc": "&quot;Executes an insert query that results in an insert ID.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\DatabaseConnectionImpl", "fromLink": "poggit/libasynql/base/DatabaseConnectionImpl.html", "link": "poggit/libasynql/base/DatabaseConnectionImpl.html#method_executeSelect", "name": "poggit\\libasynql\\base\\DatabaseConnectionImpl::executeSelect", "doc": "&quot;Executes a select query that returns an SQL result set. This does not strictly need to be SELECT queries -- reflection queries like MySQL&#039;s &lt;code&gt;SHOW TABLES&lt;\/code&gt; query are also allowed.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\DatabaseConnectionImpl", "fromLink": "poggit/libasynql/base/DatabaseConnectionImpl.html", "link": "poggit/libasynql/base/DatabaseConnectionImpl.html#method_close", "name": "poggit\\libasynql\\base\\DatabaseConnectionImpl::close", "doc": "&quot;Closes the connection and\/or all child connections. Remember to call this method when the plugin is disabled or the data provider is switched.&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\base", "fromLink": "poggit/libasynql/base.html", "link": "poggit/libasynql/base/SqlThreadPool.html", "name": "poggit\\libasynql\\base\\SqlThreadPool", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\base\\SqlThreadPool", "fromLink": "poggit/libasynql/base/SqlThreadPool.html", "link": "poggit/libasynql/base/SqlThreadPool.html#method___construct", "name": "poggit\\libasynql\\base\\SqlThreadPool::__construct", "doc": "&quot;SqlThreadPool constructor.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\SqlThreadPool", "fromLink": "poggit/libasynql/base/SqlThreadPool.html", "link": "poggit/libasynql/base/SqlThreadPool.html#method_join", "name": "poggit\\libasynql\\base\\SqlThreadPool::join", "doc": "&quot;Joins the thread&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\SqlThreadPool", "fromLink": "poggit/libasynql/base/SqlThreadPool.html", "link": "poggit/libasynql/base/SqlThreadPool.html#method_stopRunning", "name": "poggit\\libasynql\\base\\SqlThreadPool::stopRunning", "doc": "&quot;Signals the thread to stop waiting for queries when the send buffer is cleared.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\SqlThreadPool", "fromLink": "poggit/libasynql/base/SqlThreadPool.html", "link": "poggit/libasynql/base/SqlThreadPool.html#method_addQuery", "name": "poggit\\libasynql\\base\\SqlThreadPool::addQuery", "doc": "&quot;Adds a query to the queue.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\SqlThreadPool", "fromLink": "poggit/libasynql/base/SqlThreadPool.html", "link": "poggit/libasynql/base/SqlThreadPool.html#method_readResults", "name": "poggit\\libasynql\\base\\SqlThreadPool::readResults", "doc": "&quot;Handles the results that this query has completed&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\SqlThreadPool", "fromLink": "poggit/libasynql/base/SqlThreadPool.html", "link": "poggit/libasynql/base/SqlThreadPool.html#method_connCreated", "name": "poggit\\libasynql\\base\\SqlThreadPool::connCreated", "doc": "&quot;Checks if the initial connection has been made, no matter successful or not.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\SqlThreadPool", "fromLink": "poggit/libasynql/base/SqlThreadPool.html", "link": "poggit/libasynql/base/SqlThreadPool.html#method_hasConnError", "name": "poggit\\libasynql\\base\\SqlThreadPool::hasConnError", "doc": "&quot;Checks if the initial connection failed.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\SqlThreadPool", "fromLink": "poggit/libasynql/base/SqlThreadPool.html", "link": "poggit/libasynql/base/SqlThreadPool.html#method_getConnError", "name": "poggit\\libasynql\\base\\SqlThreadPool::getConnError", "doc": "&quot;Gets the error of the initial connection.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\base\\SqlThreadPool", "fromLink": "poggit/libasynql/base/SqlThreadPool.html", "link": "poggit/libasynql/base/SqlThreadPool.html#method_getLoad", "name": "poggit\\libasynql\\base\\SqlThreadPool::getLoad", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\generic", "fromLink": "poggit/libasynql/generic.html", "link": "poggit/libasynql/generic/GenericStatementFileParseException.html", "name": "poggit\\libasynql\\generic\\GenericStatementFileParseException", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementFileParseException", "fromLink": "poggit/libasynql/generic/GenericStatementFileParseException.html", "link": "poggit/libasynql/generic/GenericStatementFileParseException.html#method___construct", "name": "poggit\\libasynql\\generic\\GenericStatementFileParseException::__construct", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\generic", "fromLink": "poggit/libasynql/generic.html", "link": "poggit/libasynql/generic/GenericStatementFileParser.html", "name": "poggit\\libasynql\\generic\\GenericStatementFileParser", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementFileParser", "fromLink": "poggit/libasynql/generic/GenericStatementFileParser.html", "link": "poggit/libasynql/generic/GenericStatementFileParser.html#method___construct", "name": "poggit\\libasynql\\generic\\GenericStatementFileParser::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementFileParser", "fromLink": "poggit/libasynql/generic/GenericStatementFileParser.html", "link": "poggit/libasynql/generic/GenericStatementFileParser.html#method_parse", "name": "poggit\\libasynql\\generic\\GenericStatementFileParser::parse", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementFileParser", "fromLink": "poggit/libasynql/generic/GenericStatementFileParser.html", "link": "poggit/libasynql/generic/GenericStatementFileParser.html#method_getResults", "name": "poggit\\libasynql\\generic\\GenericStatementFileParser::getResults", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\generic", "fromLink": "poggit/libasynql/generic.html", "link": "poggit/libasynql/generic/GenericStatementImpl.html", "name": "poggit\\libasynql\\generic\\GenericStatementImpl", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementImpl", "fromLink": "poggit/libasynql/generic/GenericStatementImpl.html", "link": "poggit/libasynql/generic/GenericStatementImpl.html#method_getName", "name": "poggit\\libasynql\\generic\\GenericStatementImpl::getName", "doc": "&quot;Returns the identifier name of this query&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementImpl", "fromLink": "poggit/libasynql/generic/GenericStatementImpl.html", "link": "poggit/libasynql/generic/GenericStatementImpl.html#method_getQuery", "name": "poggit\\libasynql\\generic\\GenericStatementImpl::getQuery", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementImpl", "fromLink": "poggit/libasynql/generic/GenericStatementImpl.html", "link": "poggit/libasynql/generic/GenericStatementImpl.html#method_getVariables", "name": "poggit\\libasynql\\generic\\GenericStatementImpl::getVariables", "doc": "&quot;Returns the variables required by this statement&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementImpl", "fromLink": "poggit/libasynql/generic/GenericStatementImpl.html", "link": "poggit/libasynql/generic/GenericStatementImpl.html#method_forDialect", "name": "poggit\\libasynql\\generic\\GenericStatementImpl::forDialect", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementImpl", "fromLink": "poggit/libasynql/generic/GenericStatementImpl.html", "link": "poggit/libasynql/generic/GenericStatementImpl.html#method___construct", "name": "poggit\\libasynql\\generic\\GenericStatementImpl::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementImpl", "fromLink": "poggit/libasynql/generic/GenericStatementImpl.html", "link": "poggit/libasynql/generic/GenericStatementImpl.html#method_compilePositions", "name": "poggit\\libasynql\\generic\\GenericStatementImpl::compilePositions", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementImpl", "fromLink": "poggit/libasynql/generic/GenericStatementImpl.html", "link": "poggit/libasynql/generic/GenericStatementImpl.html#method_format", "name": "poggit\\libasynql\\generic\\GenericStatementImpl::format", "doc": "&quot;Creates a query based on the args and the backend&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericStatementImpl", "fromLink": "poggit/libasynql/generic/GenericStatementImpl.html", "link": "poggit/libasynql/generic/GenericStatementImpl.html#method_formatVariable", "name": "poggit\\libasynql\\generic\\GenericStatementImpl::formatVariable", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\generic", "fromLink": "poggit/libasynql/generic.html", "link": "poggit/libasynql/generic/GenericVariable.html", "name": "poggit\\libasynql\\generic\\GenericVariable", "doc": "&quot;Represents a variable that can be passed into {@link GenericStatement::format()}&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericVariable", "fromLink": "poggit/libasynql/generic/GenericVariable.html", "link": "poggit/libasynql/generic/GenericVariable.html#method___construct", "name": "poggit\\libasynql\\generic\\GenericVariable::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericVariable", "fromLink": "poggit/libasynql/generic/GenericVariable.html", "link": "poggit/libasynql/generic/GenericVariable.html#method_getName", "name": "poggit\\libasynql\\generic\\GenericVariable::getName", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericVariable", "fromLink": "poggit/libasynql/generic/GenericVariable.html", "link": "poggit/libasynql/generic/GenericVariable.html#method_getType", "name": "poggit\\libasynql\\generic\\GenericVariable::getType", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericVariable", "fromLink": "poggit/libasynql/generic/GenericVariable.html", "link": "poggit/libasynql/generic/GenericVariable.html#method_getDefault", "name": "poggit\\libasynql\\generic\\GenericVariable::getDefault", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericVariable", "fromLink": "poggit/libasynql/generic/GenericVariable.html", "link": "poggit/libasynql/generic/GenericVariable.html#method_isOptional", "name": "poggit\\libasynql\\generic\\GenericVariable::isOptional", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\GenericVariable", "fromLink": "poggit/libasynql/generic/GenericVariable.html", "link": "poggit/libasynql/generic/GenericVariable.html#method_format", "name": "poggit\\libasynql\\generic\\GenericVariable::format", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\generic", "fromLink": "poggit/libasynql/generic.html", "link": "poggit/libasynql/generic/MysqlStatementImpl.html", "name": "poggit\\libasynql\\generic\\MysqlStatementImpl", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\generic\\MysqlStatementImpl", "fromLink": "poggit/libasynql/generic/MysqlStatementImpl.html", "link": "poggit/libasynql/generic/MysqlStatementImpl.html#method_getDialect", "name": "poggit\\libasynql\\generic\\MysqlStatementImpl::getDialect", "doc": "&quot;Returns the dialect this query is intended for.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\MysqlStatementImpl", "fromLink": "poggit/libasynql/generic/MysqlStatementImpl.html", "link": "poggit/libasynql/generic/MysqlStatementImpl.html#method_formatVariable", "name": "poggit\\libasynql\\generic\\MysqlStatementImpl::formatVariable", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\generic", "fromLink": "poggit/libasynql/generic.html", "link": "poggit/libasynql/generic/SqliteStatementImpl.html", "name": "poggit\\libasynql\\generic\\SqliteStatementImpl", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\generic\\SqliteStatementImpl", "fromLink": "poggit/libasynql/generic/SqliteStatementImpl.html", "link": "poggit/libasynql/generic/SqliteStatementImpl.html#method_getDialect", "name": "poggit\\libasynql\\generic\\SqliteStatementImpl::getDialect", "doc": "&quot;Returns the dialect this query is intended for.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\generic\\SqliteStatementImpl", "fromLink": "poggit/libasynql/generic/SqliteStatementImpl.html", "link": "poggit/libasynql/generic/SqliteStatementImpl.html#method_formatVariable", "name": "poggit\\libasynql\\generic\\SqliteStatementImpl::formatVariable", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\mysql", "fromLink": "poggit/libasynql/mysql.html", "link": "poggit/libasynql/mysql/MysqlColumnInfo.html", "name": "poggit\\libasynql\\mysql\\MysqlColumnInfo", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlColumnInfo", "fromLink": "poggit/libasynql/mysql/MysqlColumnInfo.html", "link": "poggit/libasynql/mysql/MysqlColumnInfo.html#method___construct", "name": "poggit\\libasynql\\mysql\\MysqlColumnInfo::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlColumnInfo", "fromLink": "poggit/libasynql/mysql/MysqlColumnInfo.html", "link": "poggit/libasynql/mysql/MysqlColumnInfo.html#method_getFlags", "name": "poggit\\libasynql\\mysql\\MysqlColumnInfo::getFlags", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlColumnInfo", "fromLink": "poggit/libasynql/mysql/MysqlColumnInfo.html", "link": "poggit/libasynql/mysql/MysqlColumnInfo.html#method_hasFlag", "name": "poggit\\libasynql\\mysql\\MysqlColumnInfo::hasFlag", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlColumnInfo", "fromLink": "poggit/libasynql/mysql/MysqlColumnInfo.html", "link": "poggit/libasynql/mysql/MysqlColumnInfo.html#method_getMysqlType", "name": "poggit\\libasynql\\mysql\\MysqlColumnInfo::getMysqlType", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\mysql", "fromLink": "poggit/libasynql/mysql.html", "link": "poggit/libasynql/mysql/MysqlCredentials.html", "name": "poggit\\libasynql\\mysql\\MysqlCredentials", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlCredentials", "fromLink": "poggit/libasynql/mysql/MysqlCredentials.html", "link": "poggit/libasynql/mysql/MysqlCredentials.html#method_fromArray", "name": "poggit\\libasynql\\mysql\\MysqlCredentials::fromArray", "doc": "&quot;Creates a new {@link MysqlCredentials} instance from an array (e.g. from Config), with the following defaults:&lt;\/p&gt;\n\n&lt;pre&gt;\nhost: 127.0.0.1\nusername: root\npassword: \&quot;\&quot;\nschema: (required)\nport: 3306\nsocket: \&quot;\&quot;\n&lt;\/pre&gt;\n&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlCredentials", "fromLink": "poggit/libasynql/mysql/MysqlCredentials.html", "link": "poggit/libasynql/mysql/MysqlCredentials.html#method___construct", "name": "poggit\\libasynql\\mysql\\MysqlCredentials::__construct", "doc": "&quot;Constructs a new {@link MysqlCredentials} by passing parameters directly.&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlCredentials", "fromLink": "poggit/libasynql/mysql/MysqlCredentials.html", "link": "poggit/libasynql/mysql/MysqlCredentials.html#method_newMysqli", "name": "poggit\\libasynql\\mysql\\MysqlCredentials::newMysqli", "doc": "&quot;Creates a new &lt;a href=\&quot;https:\/\/php.net\/mysqli\&quot;&gt;mysqli&lt;\/a&gt; instance&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlCredentials", "fromLink": "poggit/libasynql/mysql/MysqlCredentials.html", "link": "poggit/libasynql/mysql/MysqlCredentials.html#method___toString", "name": "poggit\\libasynql\\mysql\\MysqlCredentials::__toString", "doc": "&quot;Produces a human-readable output without leaking password&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlCredentials", "fromLink": "poggit/libasynql/mysql/MysqlCredentials.html", "link": "poggit/libasynql/mysql/MysqlCredentials.html#method___debugInfo", "name": "poggit\\libasynql\\mysql\\MysqlCredentials::__debugInfo", "doc": "&quot;Prepares value to be var_dump()&#039;ed without leaking password&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlCredentials", "fromLink": "poggit/libasynql/mysql/MysqlCredentials.html", "link": "poggit/libasynql/mysql/MysqlCredentials.html#method_jsonSerialize", "name": "poggit\\libasynql\\mysql\\MysqlCredentials::jsonSerialize", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\mysql", "fromLink": "poggit/libasynql/mysql.html", "link": "poggit/libasynql/mysql/MysqlFlags.html", "name": "poggit\\libasynql\\mysql\\MysqlFlags", "doc": "&quot;Result field flags returned by MySQL&quot;"},
                    
            {"type": "Class", "fromName": "poggit\\libasynql\\mysql", "fromLink": "poggit/libasynql/mysql.html", "link": "poggit/libasynql/mysql/MysqlThread.html", "name": "poggit\\libasynql\\mysql\\MysqlThread", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlThread", "fromLink": "poggit/libasynql/mysql/MysqlThread.html", "link": "poggit/libasynql/mysql/MysqlThread.html#method___construct", "name": "poggit\\libasynql\\mysql\\MysqlThread::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlThread", "fromLink": "poggit/libasynql/mysql/MysqlThread.html", "link": "poggit/libasynql/mysql/MysqlThread.html#method_createConn", "name": "poggit\\libasynql\\mysql\\MysqlThread::createConn", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlThread", "fromLink": "poggit/libasynql/mysql/MysqlThread.html", "link": "poggit/libasynql/mysql/MysqlThread.html#method_executeQuery", "name": "poggit\\libasynql\\mysql\\MysqlThread::executeQuery", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\mysql\\MysqlThread", "fromLink": "poggit/libasynql/mysql/MysqlThread.html", "link": "poggit/libasynql/mysql/MysqlThread.html#method_close", "name": "poggit\\libasynql\\mysql\\MysqlThread::close", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\mysql", "fromLink": "poggit/libasynql/mysql.html", "link": "poggit/libasynql/mysql/MysqlTypes.html", "name": "poggit\\libasynql\\mysql\\MysqlTypes", "doc": "&quot;Result field types returned by MySQL&quot;"},
                    
            {"type": "Class", "fromName": "poggit\\libasynql\\result", "fromLink": "poggit/libasynql/result.html", "link": "poggit/libasynql/result/SqlChangeResult.html", "name": "poggit\\libasynql\\result\\SqlChangeResult", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\result\\SqlChangeResult", "fromLink": "poggit/libasynql/result/SqlChangeResult.html", "link": "poggit/libasynql/result/SqlChangeResult.html#method___construct", "name": "poggit\\libasynql\\result\\SqlChangeResult::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\result\\SqlChangeResult", "fromLink": "poggit/libasynql/result/SqlChangeResult.html", "link": "poggit/libasynql/result/SqlChangeResult.html#method_getAffectedRows", "name": "poggit\\libasynql\\result\\SqlChangeResult::getAffectedRows", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\result", "fromLink": "poggit/libasynql/result.html", "link": "poggit/libasynql/result/SqlColumnInfo.html", "name": "poggit\\libasynql\\result\\SqlColumnInfo", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\result\\SqlColumnInfo", "fromLink": "poggit/libasynql/result/SqlColumnInfo.html", "link": "poggit/libasynql/result/SqlColumnInfo.html#method___construct", "name": "poggit\\libasynql\\result\\SqlColumnInfo::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\result\\SqlColumnInfo", "fromLink": "poggit/libasynql/result/SqlColumnInfo.html", "link": "poggit/libasynql/result/SqlColumnInfo.html#method_getName", "name": "poggit\\libasynql\\result\\SqlColumnInfo::getName", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\result\\SqlColumnInfo", "fromLink": "poggit/libasynql/result/SqlColumnInfo.html", "link": "poggit/libasynql/result/SqlColumnInfo.html#method_getType", "name": "poggit\\libasynql\\result\\SqlColumnInfo::getType", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\result", "fromLink": "poggit/libasynql/result.html", "link": "poggit/libasynql/result/SqlInsertResult.html", "name": "poggit\\libasynql\\result\\SqlInsertResult", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\result\\SqlInsertResult", "fromLink": "poggit/libasynql/result/SqlInsertResult.html", "link": "poggit/libasynql/result/SqlInsertResult.html#method___construct", "name": "poggit\\libasynql\\result\\SqlInsertResult::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\result\\SqlInsertResult", "fromLink": "poggit/libasynql/result/SqlInsertResult.html", "link": "poggit/libasynql/result/SqlInsertResult.html#method_getInsertId", "name": "poggit\\libasynql\\result\\SqlInsertResult::getInsertId", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "poggit\\libasynql\\result", "fromLink": "poggit/libasynql/result.html", "link": "poggit/libasynql/result/SqlSelectResult.html", "name": "poggit\\libasynql\\result\\SqlSelectResult", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "poggit\\libasynql\\result\\SqlSelectResult", "fromLink": "poggit/libasynql/result/SqlSelectResult.html", "link": "poggit/libasynql/result/SqlSelectResult.html#method___construct", "name": "poggit\\libasynql\\result\\SqlSelectResult::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\result\\SqlSelectResult", "fromLink": "poggit/libasynql/result/SqlSelectResult.html", "link": "poggit/libasynql/result/SqlSelectResult.html#method_getColumnInfo", "name": "poggit\\libasynql\\result\\SqlSelectResult::getColumnInfo", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "poggit\\libasynql\\result\\SqlSelectResult", "fromLink": "poggit/libasynql/result/SqlSelectResult.html", "link": "poggit/libasynql/result/SqlSelectResult.html#method_getRows", "name": "poggit\\libasynql\\result\\SqlSelectResult::getRows", "doc": "&quot;&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


