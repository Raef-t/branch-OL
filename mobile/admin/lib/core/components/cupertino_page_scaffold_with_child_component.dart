import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';

class CupertinoPageScaffoldWithChildComponent extends StatelessWidget {
  const CupertinoPageScaffoldWithChildComponent({
    super.key,
    required this.child,
  });
  final Widget child;
  @override
  Widget build(BuildContext context) {
    return CupertinoPageScaffold(
      backgroundColor: ColorsStyle.mediumWhiteColor4,
      child: ScaffoldMessenger(
        child: Scaffold(
          extendBody: true,
          // extendBodyBehindAppBar: true,
          backgroundColor: ColorsStyle.mediumWhiteColor4,
          body: child,
        ),
      ),
    );
  }
}
