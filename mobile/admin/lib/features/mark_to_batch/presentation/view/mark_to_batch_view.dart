import 'dart:io';
import 'package:flutter/cupertino.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/mark_to_batch/presentation/view/widgets/custom_mark_to_batch_view_body.dart';

class MarkToBatchView extends StatelessWidget {
  const MarkToBatchView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomMarkToBatchViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(body: CustomMarkToBatchViewBody());
    }
  }
}
