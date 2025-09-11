#### Klarna Base Extension Integration Tests

Before executing the integration tests you need to do some setup work:
- Create integration test database
- Copy dev/tests/integration/etc/install-config-mysql.php.dist to dev/tests/integration/etc/install-config-mysql.php and fill out the details with the "db-" prefixes. If you see an error with ampq not installed, set ampq settings to empty strings.
- Copy dev/tests/integration/etc/config-global.php.dist to dev/tests/integration/etc/config-global.php (no changes needed)
- Copy dev/tests/integration/phpunit.xml.dist to dev/tests/integration/phpunit.xml. Please make sure to add an entry in the testsuites node for 3rd party integrations as suggested here: https://devdocs.magento.com/guides/v2.4/test/integration/integration_test_execution.html#execute-third-party-integration-tests
- Magento_TwoFactorAuth must be enabled, otherwise an error will be thrown:


    bin/magento module:enable Magento_TwoFactorAuth 

To execute the tests run:

    cd dev/tests/integration
    php ../../../vendor/bin/phpunit --testsuite "Third Party Integration Tests"

For further information on integration tests please visit the official Magento website: https://devdocs.magento.com/guides/v2.4/test/integration/integration_test_execution.html
