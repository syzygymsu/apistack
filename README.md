# ApiStack

ApiStack is a library of PHP classes to help implement API-s, such as JSON-RPC.
Actually, JSON-RPC is the only protocol fully supported by now, but the list should be extended.

## Stack

- *Client* (external user)
- *Transport*
  Handles communication with client, forms separate calls as plain strings.
  E.g. HTTP, socket
- *Protocol*
  Translates plain string to request object[s] and response object[s] back to plain string.
  E.g. Json-RPC, SOAP, XML-RPC?
- *Service*
  Handles request object, prepares arguments, calls requested function.
- *Functions* (your implementation)

## Usage

This library provides only base classes for three independent layers -- Transport, Protocol, and Service.
You MUST consider how every layer should behave and extend/implement them for your own needs.
