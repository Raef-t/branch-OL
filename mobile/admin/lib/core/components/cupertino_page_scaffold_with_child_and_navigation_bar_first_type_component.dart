import 'package:flutter/cupertino.dart';

class CupertinoPageScaffoldWithChildAndNavigationBarFirstTypeComponent
    extends StatelessWidget {
  const CupertinoPageScaffoldWithChildAndNavigationBarFirstTypeComponent({
    super.key,
    required this.cupertinoNavigationBar,
    required this.child,
  });
  final CupertinoNavigationBar cupertinoNavigationBar;
  final Widget child;
  @override
  Widget build(BuildContext context) {
    return CupertinoPageScaffold(
      navigationBar: cupertinoNavigationBar,
      child: child,
    );
  }
}
