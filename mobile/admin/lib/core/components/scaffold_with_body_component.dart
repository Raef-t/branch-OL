import 'package:flutter/material.dart';

class ScaffoldWithBodyComponent extends StatelessWidget {
  const ScaffoldWithBodyComponent({super.key, required this.body});
  final Widget body;
  @override
  Widget build(BuildContext context) {
    return Scaffold(body: body);
  }
}
