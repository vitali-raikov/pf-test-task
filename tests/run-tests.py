#!/usr/bin/python

import requests
import sys
from termcolor import colored

HOST = "http://nginx"
FAILURE = False

print("")
print("=== Request to / without query parameters ===")
r = requests.get(url = HOST)

print("\tShould return status code 400", end=" ")
if r.status_code == 400:
    print(colored('- Passed', 'green'))
else:
    print(colored('- Failed', 'red'))
    FAILURE = True

print("\tShould have a valid JSON error message", end=" ")
if r.text == '{"error":{"msg":"Query parameter \'n\' is not passed","code":400}}':
    print(colored('- Passed', 'green'))
else:
    print(colored('- Failed', 'red'))
    FAILURE = True



print("=== Request to / with n equals 5 ===")
r = requests.get(url = HOST, params={'n': 5})

print("\tShould return status code 200", end=" ")
if r.status_code == 200:
    print(colored('- Passed', 'green'))
else:
    print(colored('- Failed', 'red'))
    FAILURE = True

print("\tResult should equal 25", end=" ")
if r.text == '25':
    print(colored('- Passed', 'green'))
else:
    print(colored('- Failed', 'red'))
    FAILURE = True



print("=== Request to /blacklisted ===")
r = requests.get(url = HOST+"/blacklisted")

print("\tShould return status code 444", end=" ")
if r.status_code == 444:
    print(colored('- Passed', 'green'))
else:
    print(colored('- Failed', 'red'))
    FAILURE = True

print("\tShould contain a message about IP blocking", end=" ")
if r.text == 'Your IP was succesfully blacklisted':
    print(colored('- Passed', 'green'))
else:
    print(colored('- Failed', 'red'))
    FAILURE = True



print("=== Request to /status ===")
r = requests.get(url = HOST+"/status")

print("\tShould return status code 200", end=" ")
if r.status_code == 200:
    print(colored('- Passed', 'green'))
else:
    print(colored('- Failed', 'red'))
    FAILURE = True

print("\tShould return Healthy despite being blacklisted", end=" ")
if r.text == 'Healthy':
    print(colored('- Passed', 'green'))
else:
    print(colored('- Failed', 'red'))
    FAILURE = True


for endpoint in ["/index", "/blacklisted"]:
    print("=== Request to "+endpoint+" while blacklisted ===")
    r = requests.get(url = HOST+endpoint)

    print("\tShould return status code 444", end=" ")
    if r.status_code == 444:
        print(colored('- Passed', 'green'))
    else:
        print(colored('- Failed', 'red'))
        FAILURE = True

    print("\tShould contain a message about IP being blocked", end=" ")
    if "Your IP has been blocked at" in r.text:
        print(colored('- Passed', 'green'))
    else:
        print(colored('- Failed', 'red'))
        FAILURE = True




print("=== Request to /unblock ===")
r = requests.get(url = HOST+"/unblock")

print("\tShould return status code 200", end=" ")
if r.status_code == 200:
    print(colored('- Passed', 'green'))
else:
    print(colored('- Failed', 'red'))
    FAILURE = True

print("\tShould return message about being unblocked", end=" ")
if r.text == 'Succesfully unblocked':
    print(colored('- Passed', 'green'))
else:
    print(colored('- Failed', 'red'))
    FAILURE = True

print("")

if FAILURE == True:
    print(colored('Tests had Failed', 'red'))
    sys.exit(1)
else:
    print(colored('All tests had passed succesfully', 'green'))
