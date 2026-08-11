#!/usr/bin/env bash

function tear_down() {
  rm -f tests/EndToEndBash/Fixtures/*.fixed
}

function test_phpcs_exit_code_clean_file() {
  bin/phpcs --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist tests/EndToEndBash/Fixtures/ClassOneWithoutStyleError.inc
  assert_exit_code 0
}
function test_phpcs_exit_code_clean_stdin() {
  bin/phpcs --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist < tests/EndToEndBash/Fixtures/ClassOneWithoutStyleError.inc
  assert_exit_code 0
}
function test_phpcbf_exit_code_clean_file() {
  bin/phpcbf --suffix=.fixed --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist tests/EndToEndBash/Fixtures/ClassOneWithoutStyleError.inc
  assert_exit_code 0
}
function test_phpcbf_exit_code_clean_stdin() {
  bin/phpcbf --suffix=.fixed --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist < tests/EndToEndBash/Fixtures/ClassOneWithoutStyleError.inc
  assert_exit_code 0
}

function test_phpcs_exit_code_fixable_file() {
  bin/phpcs --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist tests/EndToEndBash/Fixtures/ClassWithStyleError.inc
  assert_exit_code 1
}
function test_phpcs_exit_code_fixable_stdin() {
  bin/phpcs --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist < tests/EndToEndBash/Fixtures/ClassWithStyleError.inc
  assert_exit_code 1
}
function test_phpcbf_exit_code_fixable_file() {
  bin/phpcbf --suffix=.fixed --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist tests/EndToEndBash/Fixtures/ClassWithStyleError.inc
  assert_exit_code 0
}
function test_phpcbf_exit_code_fixable_stdin() {
  bin/phpcbf --suffix=.fixed --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist < tests/EndToEndBash/Fixtures/ClassWithStyleError.inc
  assert_exit_code 0
}

function test_phpcs_exit_code_non_fixable_file() {
  bin/phpcs --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist tests/EndToEndBash/Fixtures/ClassWithUnfixableStyleError.inc
  assert_exit_code 2
}
function test_phpcs_exit_code_non_fixable_stdin() {
  bin/phpcs --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist < tests/EndToEndBash/Fixtures/ClassWithUnfixableStyleError.inc
  assert_exit_code 2
}
function test_phpcbf_exit_code_non_fixable_file() {
  bin/phpcbf --suffix=.fixed --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist tests/EndToEndBash/Fixtures/ClassWithUnfixableStyleError.inc
  assert_exit_code 2
}
function test_phpcbf_exit_code_non_fixable_stdin() {
  bin/phpcbf --suffix=.fixed --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist < tests/EndToEndBash/Fixtures/ClassWithUnfixableStyleError.inc
  assert_exit_code 2
}

function test_phpcs_exit_code_fixable_and_non_fixable_file() {
  bin/phpcs --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist tests/EndToEndBash/Fixtures/ClassWithTwoStyleErrors.inc
  assert_exit_code 3
}
function test_phpcs_exit_code_fixable_and_non_fixable_stdin() {
  bin/phpcs --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist < tests/EndToEndBash/Fixtures/ClassWithTwoStyleErrors.inc
  assert_exit_code 3
}
function test_phpcbf_exit_code_fixable_and_non_fixable_file() {
  bin/phpcbf --suffix=.fixed --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist tests/EndToEndBash/Fixtures/ClassWithTwoStyleErrors.inc
  assert_exit_code 2
}
function test_phpcbf_exit_code_fixable_and_non_fixable_stdin() {
  bin/phpcbf --suffix=.fixed --standard=tests/EndToEndBash/Fixtures/endtoend.xml.dist < tests/EndToEndBash/Fixtures/ClassWithTwoStyleErrors.inc
  assert_exit_code 2
}
