import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

void goRouterHelper({
  required BuildContext context,
  required String view,
  Object? extraObject,
}) {
  GoRouter.of(context).go(view, extra: extraObject);
}
