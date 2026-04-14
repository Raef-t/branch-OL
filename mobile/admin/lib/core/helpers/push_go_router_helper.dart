import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

void pushGoRouterHelper({
  required BuildContext context,
  required String view,
  Object? extraObject,
}) {
  GoRouter.of(context).push(view, extra: extraObject);
}
