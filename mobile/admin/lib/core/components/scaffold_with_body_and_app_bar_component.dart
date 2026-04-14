import 'package:flutter/material.dart';

class ScaffoldWithBodyAndAppBarComponent extends StatelessWidget {
  const ScaffoldWithBodyAndAppBarComponent({
    super.key,
    required this.appBar,
    required this.body,
  });
  final AppBar appBar;
  final Widget body;
  @override
  Widget build(BuildContext context) {
    return Scaffold(appBar: appBar, body: body);
  }
}
